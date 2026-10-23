<?php

namespace App\Filament\Pages;

use App\Models\Book;
use App\Models\Comment;
use App\Models\Discount;
use App\Models\Enrollment;
use App\Models\Fee;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoiceType;
use App\Models\Payment;
use App\Models\Paymentmethod;
use App\Models\ScheduledPayment;
use App\Models\Tax;
use App\Traits\ReportsErrors;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action as PageAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class CheckoutPage extends Page
{
    use ReportsErrors;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.checkout';

    public ?array $data = [];

    public ?Enrollment $enrollment = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('enrollments.edit') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getNavigationLabel(): string
    {
        return __('Checkout');
    }

    public function getTitle(): string
    {
        return __('Checkout');
    }

    public function mount(): void
    {
        $products = [];
        $clientData = [];

        // Pre-populate from enrollment
        if ($enrollmentId = request('enrollment_id')) {
            $this->enrollment = Enrollment::with(['student.user', 'student.contacts', 'course.books'])->find($enrollmentId);

            if ($this->enrollment) {
                // Add default fees
                foreach (Fee::where('default', 1)->get() as $fee) {
                    $products[] = [
                        'product_name' => $fee->name,
                        'product_code' => $fee->product_code ?? '',
                        'product_type' => Fee::class,
                        'product_id' => $fee->id,
                        'price' => $fee->price,
                        'quantity' => 1,
                        'comment' => '',
                    ];
                }

                // Add the enrollment itself
                $products[] = [
                    'product_name' => $this->enrollment->course->name,
                    'product_code' => $this->enrollment->course->product_code ?? '',
                    'product_type' => Enrollment::class,
                    'product_id' => $this->enrollment->id,
                    'price' => $this->enrollment->total_price ?? $this->enrollment->course->price ?? 0,
                    'quantity' => 1,
                    'comment' => '',
                ];

                // Add books if configured
                if (config('invoicing.add_books_to_invoices')) {
                    foreach ($this->enrollment->course->books as $book) {
                        $products[] = [
                            'product_name' => $book->name,
                            'product_code' => $book->product_code ?? '',
                            'product_type' => Book::class,
                            'product_id' => $book->id,
                            'price' => $book->price,
                            'quantity' => 1,
                            'comment' => '',
                        ];
                    }
                }

                // Add default taxes
                foreach (Tax::where('default', true)->get() as $tax) {
                    $enrollmentPrice = $this->enrollment->total_price ?? $this->enrollment->course->price ?? 0;
                    $taxAmount = round($enrollmentPrice * $tax->value / 100, 2);
                    $products[] = [
                        'product_name' => $tax->name,
                        'product_code' => '',
                        'product_type' => Tax::class,
                        'product_id' => $tax->id,
                        'price' => $taxAmount,
                        'quantity' => 1,
                        'comment' => '',
                    ];
                }

                // Pre-fill client data
                $student = $this->enrollment->student;
                $user = $student?->user;
                if ($user) {
                    $clientData = [
                        'client_name' => $user->firstname.' '.$user->lastname,
                        'client_email' => $user->email,
                        'client_phone' => $student->phone->first()?->phone_number ?? '',
                        'client_idnumber' => $student->idnumber ?? '',
                        'client_address' => $student->address ?? '',
                    ];
                }
            }
        }

        // Pre-populate from scheduled payment
        if ($scheduledPaymentId = request('scheduled_payment_id')) {
            $sp = ScheduledPayment::with(['enrollment.student.user'])->find($scheduledPaymentId);
            if ($sp) {
                $this->enrollment = $sp->enrollment;

                $products[] = [
                    'product_name' => __('Scheduled Payment').' - '.$sp->date?->format('Y-m-d'),
                    'product_code' => '',
                    'product_type' => ScheduledPayment::class,
                    'product_id' => $sp->id,
                    'price' => $sp->value,
                    'quantity' => 1,
                    'comment' => '',
                ];

                $student = $sp->enrollment->student ?? null;
                $user = $student?->user;
                if ($user) {
                    $clientData = [
                        'client_name' => $user->firstname.' '.$user->lastname,
                        'client_email' => $user->email,
                        'client_phone' => $student->phone->first()?->phone_number ?? '',
                        'client_idnumber' => $student->idnumber ?? '',
                        'client_address' => $student->address ?? '',
                    ];
                }
            }
        }

        if (! $this->enrollment && ! request('scheduled_payment_id')) {
            abort(404);
        }

        $total = collect($products)->sum(fn ($p) => ($p['price'] ?? 0) * ($p['quantity'] ?? 1));

        $this->data = [
            'products' => $products ?: [['product_name' => '', 'product_code' => '', 'price' => 0, 'quantity' => 1, 'comment' => '']],
            'client_name' => $clientData['client_name'] ?? '',
            'client_email' => $clientData['client_email'] ?? '',
            'client_phone' => $clientData['client_phone'] ?? '',
            'client_idnumber' => $clientData['client_idnumber'] ?? '',
            'client_address' => $clientData['client_address'] ?? '',
            'invoice_type_id' => InvoiceType::first()?->id,
            'date' => now()->format('Y-m-d'),
            'payments' => [['payment_method' => null, 'value' => $total, 'date' => now()->format('Y-m-d')]],
            'add_book' => null,
            'add_fee' => null,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Wizard::make([
                    Wizard\Step::make(__('Cart'))
                        ->icon(Heroicon::OutlinedShoppingCart)
                        ->columns(12)
                        ->afterValidation(function () {
                            $products = $this->data['products'] ?? [];
                            $total = collect($products)->sum(fn ($p) => ((float) ($p['price'] ?? 0)) * ((float) ($p['quantity'] ?? 1)));

                            foreach ($this->data['payments'] as $index => $payment) {
                                if ($index === 0) {
                                    $this->data['payments'][$index]['value'] = $total;
                                }
                            }
                        })
                        ->schema([
                            // Main content — products
                            Section::make(__('Products'))
                                ->columnSpan(8)
                                ->schema([
                                    Repeater::make('products')
                                        ->label('')
                                        ->schema([
                                            TextInput::make('product_name')
                                                ->label(__('Product'))
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),
                                            TextInput::make('quantity')
                                                ->label(__('Qty'))
                                                ->numeric()
                                                ->default(1)
                                                ->minValue(0.01)
                                                ->step(0.01)
                                                ->required()
                                                ->live(onBlur: true)
                                                ->columnSpan(1),
                                            TextInput::make('price')
                                                ->label(__('Unit Price'))
                                                ->numeric()
                                                ->required()
                                                ->step(0.01)
                                                ->live(onBlur: true)
                                                ->prefix(config('academico.currency_position') === 'before' ? config('academico.currency_symbol') : null)
                                                ->suffix(config('academico.currency_position') === 'after' ? config('academico.currency_symbol') : null)
                                                ->columnSpan(2),
                                            TextInput::make('comment')
                                                ->label(__('Comment'))
                                                ->maxLength(255)
                                                ->columnSpan(3),
                                            Hidden::make('product_type'),
                                            Hidden::make('product_id'),
                                            Hidden::make('product_code'),
                                        ])
                                        ->columns(9)
                                        ->defaultItems(1)
                                        ->addActionLabel(__('Add Product'))
                                        ->reorderable(false)
                                        ->extraItemActions([
                                            PageAction::make('addDiscount')
                                                ->label(__('Add discount'))
                                                ->icon(Heroicon::OutlinedReceiptPercent)
                                                ->form([
                                                    Select::make('discount_id')
                                                        ->label(__('Discount'))
                                                        ->options(Discount::pluck('name', 'id'))
                                                        ->required(),
                                                ])
                                                ->action(function (array $data, array $arguments, Repeater $component): void {
                                                    $discount = Discount::find($data['discount_id']);
                                                    if (! $discount) {
                                                        return;
                                                    }

                                                    $items = $component->getRawState();
                                                    $itemPrice = (float) ($items[$arguments['item']]['price'] ?? 0);
                                                    $discountAmount = round($itemPrice * $discount->value / 100, 2);

                                                    $items[] = [
                                                        'product_name' => $discount->name,
                                                        'product_code' => '',
                                                        'product_type' => Discount::class,
                                                        'product_id' => $discount->id,
                                                        'price' => -$discountAmount,
                                                        'quantity' => 1,
                                                        'comment' => '',
                                                    ];

                                                    $component->rawState($items);
                                                }),
                                            PageAction::make('addTax')
                                                ->label(__('Add tax'))
                                                ->icon(Heroicon::OutlinedCalculator)
                                                ->form([
                                                    Select::make('tax_id')
                                                        ->label(__('Tax'))
                                                        ->options(Tax::pluck('name', 'id'))
                                                        ->required(),
                                                ])
                                                ->action(function (array $data, array $arguments, Repeater $component): void {
                                                    $tax = Tax::find($data['tax_id']);
                                                    if (! $tax) {
                                                        return;
                                                    }

                                                    $items = $component->getRawState();
                                                    $itemPrice = (float) ($items[$arguments['item']]['price'] ?? 0);
                                                    $taxAmount = round($itemPrice * $tax->value / 100, 2);

                                                    $items[] = [
                                                        'product_name' => $tax->name,
                                                        'product_code' => '',
                                                        'product_type' => Tax::class,
                                                        'product_id' => $tax->id,
                                                        'price' => $taxAmount,
                                                        'quantity' => 1,
                                                        'comment' => '',
                                                    ];

                                                    $component->rawState($items);
                                                }),
                                        ]),
                                    Placeholder::make('total')
                                        ->label(__('Total'))
                                        ->content(fn ($get) => $this->getFormattedTotal($get('products'))),
                                ]),

                            // Sidebar
                            Section::make(__('Add products'))
                                ->columnSpan(4)
                                ->schema([
                                    Select::make('add_book')
                                        ->label(__('Book'))
                                        ->options(fn () => $this->enrollment?->course?->books?->pluck('name', 'id') ?? [])
                                        ->placeholder(__('Select a book'))
                                        ->live()
                                        ->afterStateUpdated(fn ($state, $set, $get) => $this->addBook($state, $set, $get))
                                        ->visible(fn () => $this->enrollment?->course?->books?->isNotEmpty()),
                                    Select::make('add_fee')
                                        ->label(__('Fee'))
                                        ->options(Fee::pluck('name', 'id'))
                                        ->placeholder(__('Select a fee'))
                                        ->live()
                                        ->afterStateUpdated(fn ($state, $set, $get) => $this->addFee($state, $set, $get))
                                        ->visible(fn () => ! config('invoicing.invoices_contain_enrollments_only')),
                                    Section::make(__('Price category'))
                                        ->visible(fn () => config('invoicing.price_categories_enabled') && $this->enrollment)
                                        ->schema([
                                            Placeholder::make('price_category_buttons')
                                                ->label('')
                                                ->content(fn () => new HtmlString(
                                                    '<div class="flex flex-col gap-2">'
                                                    .'<button type="button" wire:click="setPriceCategory(\'price\')" class="fi-btn fi-btn-size-sm fi-color-gray rounded-lg px-3 py-1.5 text-sm font-medium text-gray-700 bg-white shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-600">'.__('Price A').' ('.($this->enrollment?->course?->price ?? 0).')</button>'
                                                    .'<button type="button" wire:click="setPriceCategory(\'price_b\')" class="fi-btn fi-btn-size-sm fi-color-gray rounded-lg px-3 py-1.5 text-sm font-medium text-gray-700 bg-white shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-600">'.__('Price B').' ('.($this->enrollment?->course?->price_b ?? 0).')</button>'
                                                    .'<button type="button" wire:click="setPriceCategory(\'price_c\')" class="fi-btn fi-btn-size-sm fi-color-gray rounded-lg px-3 py-1.5 text-sm font-medium text-gray-700 bg-white shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-600">'.__('Price C').' ('.($this->enrollment?->course?->price_c ?? 0).')</button>'
                                                    .'</div>'
                                                )),
                                        ]),
                                ]),
                        ]),

                    Wizard\Step::make(__('Payment'))
                        ->icon(Heroicon::OutlinedCreditCard)
                        ->columns(12)
                        ->schema([
                            // Main content
                            Section::make(__('Products Summary'))
                                ->columnSpan(8)
                                ->schema([
                                    Placeholder::make('products_summary')
                                        ->label('')
                                        ->content(fn ($get) => new HtmlString($this->getProductsSummaryHtml($get('products')))),
                                    Repeater::make('payments')
                                        ->label(__('Payments'))
                                        ->schema([
                                            Select::make('payment_method')
                                                ->label(__('Payment Method'))
                                                ->options(Paymentmethod::pluck('name', 'code'))
                                                ->searchable()
                                                ->preload()
                                                ->required(fn () => ! config('invoicing.allow_empty_payment_methods')),
                                            TextInput::make('value')
                                                ->label(__('Amount'))
                                                ->numeric()
                                                ->required()
                                                ->step(0.01)
                                                ->live(onBlur: true)
                                                ->prefix(config('academico.currency_position') === 'before' ? config('academico.currency_symbol') : null)
                                                ->suffix(config('academico.currency_position') === 'after' ? config('academico.currency_symbol') : null),
                                            DatePicker::make('date')
                                                ->label(__('Date'))
                                                ->default(now()),
                                        ])
                                        ->columns(3)
                                        ->defaultItems(1)
                                        ->addActionLabel(__('Add Payment'))
                                        ->reorderable(false),
                                    Placeholder::make('total_received')
                                        ->label(__('Total received'))
                                        ->content(fn ($get) => $this->getFormattedPaymentTotal($get('payments'))),
                                ]),

                            // Sidebar — Invoice Data
                            Section::make(__('Invoice Data'))
                                ->columnSpan(4)
                                ->collapsed(fn () => config('invoicing.skip_invoice_data_step'))
                                ->schema([
                                    TextInput::make('client_name')
                                        ->label(__('Name'))
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('client_idnumber')
                                        ->label(__('ID Number'))
                                        ->maxLength(255),
                                    TextInput::make('client_address')
                                        ->label(__('Address'))
                                        ->maxLength(255),
                                    TextInput::make('client_email')
                                        ->label(__('Email'))
                                        ->email()
                                        ->maxLength(255),
                                    TextInput::make('client_phone')
                                        ->label(__('Phone'))
                                        ->maxLength(255),
                                    Select::make('invoice_type_id')
                                        ->label(__('Invoice Type'))
                                        ->options(InvoiceType::pluck('name', 'id'))
                                        ->required()
                                        ->preload(),
                                    DatePicker::make('date')
                                        ->label(__('Date'))
                                        ->default(now())
                                        ->required(),
                                    TextInput::make('receipt_number')
                                        ->label(__('Receipt Number'))
                                        ->maxLength(255)
                                        ->visible(fn () => config('invoicing.invoice_numbering') === 'manual'),
                                    TextInput::make('invoice_comment')
                                        ->label(__('Comment'))
                                        ->maxLength(500),
                                ]),
                        ]),
                ])
                    ->submitAction(view('filament.pages.checkout-submit-button')),
            ])
            ->statePath('data');
    }

    public function addBook(?string $bookId, $set, $get): void
    {
        if (! $bookId) {
            return;
        }

        $book = Book::find($bookId);
        if (! $book) {
            return;
        }

        $products = $get('products') ?? [];
        $products[] = [
            'product_name' => $book->name,
            'product_code' => $book->product_code ?? '',
            'product_type' => Book::class,
            'product_id' => $book->id,
            'price' => $book->price,
            'quantity' => 1,
            'comment' => '',
        ];

        $set('products', $products);
        $set('add_book', null);
    }

    public function addFee(?string $feeId, $set, $get): void
    {
        if (! $feeId) {
            return;
        }

        $fee = Fee::find($feeId);
        if (! $fee) {
            return;
        }

        $products = $get('products') ?? [];
        $products[] = [
            'product_name' => $fee->name,
            'product_code' => $fee->product_code ?? '',
            'product_type' => Fee::class,
            'product_id' => $fee->id,
            'price' => $fee->price,
            'quantity' => 1,
            'comment' => '',
        ];

        $set('products', $products);
        $set('add_fee', null);
    }

    public function setPriceCategory(string $field): void
    {
        if (! $this->enrollment) {
            return;
        }

        $course = $this->enrollment->course;
        $newPrice = $course->$field ?? 0;

        $products = $this->data['products'] ?? [];
        foreach ($products as $key => $product) {
            if (($product['product_type'] ?? '') === Enrollment::class && (int) ($product['product_id'] ?? 0) === $this->enrollment->id) {
                $products[$key]['price'] = $newPrice;
                break;
            }
        }

        $this->data['products'] = $products;
    }

    protected function getFormattedTotal(?array $products = null): string
    {
        $products = $products ?? $this->data['products'] ?? [];
        $total = collect($products)->sum(fn ($p) => ((float) ($p['price'] ?? 0)) * ((float) ($p['quantity'] ?? 1)));
        $formatted = number_format($total, 2);

        if (config('academico.currency_position') === 'before') {
            return config('academico.currency_symbol').' '.$formatted;
        }

        return $formatted.' '.config('academico.currency_symbol');
    }

    protected function getFormattedPaymentTotal(?array $payments = null): string
    {
        $payments = $payments ?? $this->data['payments'] ?? [];
        $total = collect($payments)->sum(fn ($p) => (float) ($p['value'] ?? 0));
        $formatted = number_format($total, 2);

        if (config('academico.currency_position') === 'before') {
            return config('academico.currency_symbol').' '.$formatted;
        }

        return $formatted.' '.config('academico.currency_symbol');
    }

    protected function getProductsSummaryHtml(?array $products = null): string
    {
        $products = $products ?? $this->data['products'] ?? [];
        $currencySymbol = config('academico.currency_symbol', '$');
        $currencyBefore = config('academico.currency_position') === 'before';

        $rows = '';
        $total = 0;
        foreach ($products as $product) {
            $price = (float) ($product['price'] ?? 0);
            $qty = (float) ($product['quantity'] ?? 1);
            $lineTotal = $price * $qty;
            $total += $lineTotal;
            $formattedPrice = $currencyBefore ? $currencySymbol.' '.number_format($price, 2) : number_format($price, 2).' '.$currencySymbol;
            $formattedLineTotal = $currencyBefore ? $currencySymbol.' '.number_format($lineTotal, 2) : number_format($lineTotal, 2).' '.$currencySymbol;
            $name = e($product['product_name'] ?? '');
            $comment = e($product['comment'] ?? '');
            $rows .= "<tr><td class='px-3 py-2'>{$name}</td><td class='px-3 py-2 text-right'>{$qty}</td><td class='px-3 py-2 text-right'>{$formattedPrice}</td><td class='px-3 py-2 text-right'>{$formattedLineTotal}</td></tr>";
            if ($comment) {
                $rows .= "<tr><td colspan='4' class='px-3 pb-2 text-sm text-gray-500'>{$comment}</td></tr>";
            }
        }

        $formattedTotal = $currencyBefore ? $currencySymbol.' '.number_format($total, 2) : number_format($total, 2).' '.$currencySymbol;

        return '<table class="w-full text-sm">'
            .'<thead><tr class="border-b">'
            .'<th class="px-3 py-2 text-left">'.__('Product').'</th>'
            .'<th class="px-3 py-2 text-right">'.__('Qty').'</th>'
            .'<th class="px-3 py-2 text-right">'.__('Unit Price').'</th>'
            .'<th class="px-3 py-2 text-right">'.__('Total').'</th>'
            .'</tr></thead>'
            .'<tbody>'.$rows.'</tbody>'
            .'<tfoot><tr class="border-t font-bold">'
            .'<td colspan="3" class="px-3 py-2 text-right">'.__('Total').'</td>'
            .'<td class="px-3 py-2 text-right">'.$formattedTotal.'</td>'
            .'</tr></tfoot>'
            .'</table>';
    }

    public function submit(): void
    {
        $data = $this->data;

        try {
            $invoice = DB::transaction(function () use ($data) {
                // 1. Create the Invoice
                $invoice = Invoice::create([
                    'client_name' => $data['client_name'],
                    'client_idnumber' => $data['client_idnumber'] ?? null,
                    'client_address' => $data['client_address'] ?? null,
                    'client_email' => $data['client_email'] ?? null,
                    'client_phone' => $data['client_phone'] ?? null,
                    'invoice_type_id' => $data['invoice_type_id'],
                    'receipt_number' => $data['receipt_number'] ?? null,
                    'date' => Carbon::parse($data['date']),
                ]);

                $invoice->setNumber();

                // 2. Create InvoiceDetails for each product
                foreach ($data['products'] as $product) {
                    $price = (float) ($product['price'] ?? 0);
                    $priceInCents = (int) round($price * 100);

                    InvoiceDetail::create([
                        'invoice_id' => $invoice->id,
                        'product_name' => $product['product_name'],
                        'product_code' => $product['product_code'] ?? null,
                        'product_id' => $product['product_id'] ?? null,
                        'product_type' => $product['product_type'] ?? null,
                        'price' => $price,
                        'final_price' => $priceInCents,
                        'quantity' => $product['quantity'] ?? 1,
                        'comment' => $product['comment'] ?? null,
                    ]);
                }

                // 3. Create Payment records
                foreach ($data['payments'] ?? [] as $payment) {
                    if (empty($payment['value']) || $payment['value'] <= 0) {
                        continue;
                    }

                    Payment::create([
                        'responsable_id' => Auth::id(),
                        'invoice_id' => $invoice->id,
                        'payment_method' => $payment['payment_method'] ?? null,
                        'value' => $payment['value'],
                        'date' => $payment['date'] ? Carbon::parse($payment['date']) : now(),
                    ]);
                }

                // 4. Optional comment
                if (! empty($data['invoice_comment'])) {
                    Comment::create([
                        'body' => $data['invoice_comment'],
                        'commentable_id' => $invoice->id,
                        'commentable_type' => Invoice::class,
                        'author_id' => Auth::id(),
                    ]);
                }

                // 5. Mark products as paid if invoice is fully paid
                $this->markPaidProductsIfFullyPaid($invoice);

                return $invoice;
            });

            Notification::make()
                ->title(__('Invoice created successfully'))
                ->success()
                ->send();

            if ($this->enrollment) {
                $this->redirect(route('filament.admin.resources.enrollments.view', $this->enrollment));
            } else {
                $this->redirect(route('filament.admin.resources.invoices.view', $invoice));
            }
        } catch (\Throwable $e) {
            $this->reportError($e, 'CheckoutPage::submit', [
                'enrollment_id' => $this->enrollment?->id,
            ]);

            Notification::make()
                ->title(__('An error occurred while creating the invoice.'))
                ->danger()
                ->send();
        }
    }

    protected function markPaidProductsIfFullyPaid(Invoice $invoice): void
    {
        $invoice->load(['invoiceDetails', 'payments']);

        if ($invoice->totalPrice() <= 0 || $invoice->totalPrice() > $invoice->paidTotal()) {
            return;
        }

        // Mark scheduled payments as paid
        foreach ($invoice->scheduledPayments as $detail) {
            $sp = $detail->product;
            if ($sp instanceof ScheduledPayment) {
                $sp->markAsPaid();

                // If all scheduled payments for this enrollment are paid, mark enrollment as paid
                $enrollment = $sp->enrollment;
                if ($enrollment && $enrollment->scheduledPayments()->where('status', '!=', 2)->doesntExist()) {
                    $enrollment->update(['status_id' => 2]);
                }
            }
        }

        // Mark enrollments as paid
        foreach ($invoice->enrollments as $detail) {
            $enrollment = $detail->product;
            if ($enrollment instanceof Enrollment) {
                $enrollment->update(['status_id' => 2]);
            }
        }
    }
}
