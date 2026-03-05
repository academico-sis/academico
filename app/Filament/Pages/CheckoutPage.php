<?php

namespace App\Filament\Pages;

use App\Interfaces\InvoicingInterface;
use App\Models\Book;
use App\Models\Comment;
use App\Models\Enrollment;
use App\Models\Fee;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoiceType;
use App\Models\Payment;
use App\Models\Paymentmethod;
use App\Models\ScheduledPayment;
use BackedEnum;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class CheckoutPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.checkout';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->can('enrollments.edit') ?? false;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'A revoir / WIP';
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
            $enrollment = Enrollment::with(['student.user', 'student.contacts', 'course.books'])->find($enrollmentId);
            if ($enrollment) {
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
                    'product_name' => $enrollment->course->name,
                    'product_code' => $enrollment->course->product_code ?? '',
                    'product_type' => Enrollment::class,
                    'product_id' => $enrollment->id,
                    'price' => $enrollment->total_price ?? $enrollment->course->price ?? 0,
                    'quantity' => 1,
                    'comment' => '',
                ];

                // Add books if configured
                if (config('invoicing.add_books_to_invoices')) {
                    foreach ($enrollment->course->books as $book) {
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

                // Pre-fill client data
                $student = $enrollment->student;
                $user = $student?->user;
                if ($user) {
                    $clientData = [
                        'client_name' => $user->firstname.' '.$user->lastname,
                        'client_email' => $user->email,
                        'client_phone' => $student->phone ?? '',
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
                $products[] = [
                    'product_name' => __('Scheduled Payment').' - '.$sp->date,
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
                        'client_phone' => $student->phone ?? '',
                        'client_idnumber' => $student->idnumber ?? '',
                        'client_address' => $student->address ?? '',
                    ];
                }
            }
        }

        $this->form->fill([
            'products' => $products ?: [['product_name' => '', 'product_code' => '', 'price' => 0, 'quantity' => 1, 'comment' => '']],
            'client_name' => $clientData['client_name'] ?? '',
            'client_email' => $clientData['client_email'] ?? '',
            'client_phone' => $clientData['client_phone'] ?? '',
            'client_idnumber' => $clientData['client_idnumber'] ?? '',
            'client_address' => $clientData['client_address'] ?? '',
            'invoice_type_id' => InvoiceType::first()?->id,
            'date' => now()->format('Y-m-d'),
            'payments' => [['payment_method' => null, 'value' => 0, 'date' => now()->format('Y-m-d')]],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make(__('Products'))
                        ->icon(Heroicon::OutlinedShoppingBag)
                        ->schema([
                            Repeater::make('products')
                                ->label('')
                                ->schema([
                                    TextInput::make('product_name')
                                        ->label(__('Product'))
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpan(3),
                                    TextInput::make('product_code')
                                        ->label(__('Code'))
                                        ->maxLength(255)
                                        ->columnSpan(1),
                                    TextInput::make('quantity')
                                        ->label(__('Qty'))
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(0.01)
                                        ->step(0.01)
                                        ->required()
                                        ->columnSpan(1),
                                    TextInput::make('price')
                                        ->label(__('Unit Price'))
                                        ->numeric()
                                        ->required()
                                        ->step(0.01)
                                        ->prefix(config('academico.currency_position') === 'before' ? config('academico.currency_symbol') : null)
                                        ->suffix(config('academico.currency_position') === 'after' ? config('academico.currency_symbol') : null)
                                        ->columnSpan(2),
                                    TextInput::make('comment')
                                        ->label(__('Comment'))
                                        ->maxLength(255)
                                        ->columnSpan(2),
                                    Select::make('product_type')
                                        ->label(__('Type'))
                                        ->options([
                                            Enrollment::class => __('Enrollment'),
                                            Fee::class => __('Fee'),
                                            Book::class => __('Book'),
                                            ScheduledPayment::class => __('Scheduled Payment'),
                                        ])
                                        ->columnSpan(2),
                                    TextInput::make('product_id')
                                        ->label(__('Product ID'))
                                        ->numeric()
                                        ->columnSpan(1),
                                ])
                                ->columns(12)
                                ->defaultItems(1)
                                ->addActionLabel(__('Add Product'))
                                ->reorderable(false),
                        ]),

                    Wizard\Step::make(__('Client'))
                        ->icon(Heroicon::OutlinedUser)
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
                        ]),

                    Wizard\Step::make(__('Payment'))
                        ->icon(Heroicon::OutlinedCreditCard)
                        ->schema([
                            Section::make(__('Invoice Details'))
                                ->schema([
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
                                        ->visible(fn () => config('invoicing.invoice_numbering') === 'manual'),
                                    TextInput::make('comment')
                                        ->label(__('Comment'))
                                        ->maxLength(500),
                                    Toggle::make('send_to_accounting')
                                        ->label(__('Send to external accounting'))
                                        ->visible(fn () => config('invoicing.accounting_enabled')),
                                ]),

                            Section::make(__('Payments'))
                                ->schema([
                                    Repeater::make('payments')
                                        ->label('')
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
                                ]),
                        ]),
                ])
                    ->submitAction(view('filament.pages.checkout-submit-button')),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

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
            $priceInCents = (int) round(($product['price'] ?? 0) * 100);

            InvoiceDetail::create([
                'invoice_id' => $invoice->id,
                'product_name' => $product['product_name'],
                'product_code' => $product['product_code'] ?? null,
                'product_id' => $product['product_id'] ?? null,
                'product_type' => $product['product_type'] ?? null,
                'price' => $priceInCents,
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

        // 4. Optional external accounting
        if (! empty($data['send_to_accounting']) && config('invoicing.accounting_enabled')) {
            $invoicingSystem = config('invoicing.invoicing_system');
            $serviceClass = config("invoicing.{$invoicingSystem}.class");
            if ($serviceClass) {
                $service = app($serviceClass);
                if ($service instanceof InvoicingInterface) {
                    $result = $service->saveInvoice($invoice);
                    if ($result && $result !== 'ok') {
                        $invoice->update(['receipt_number' => $result]);
                    }
                }
            }
        }

        // 5. Optional comment
        if (! empty($data['comment'])) {
            Comment::create([
                'body' => $data['comment'],
                'commentable_id' => $invoice->id,
                'commentable_type' => Invoice::class,
                'author_id' => Auth::id(),
            ]);
        }

        // 6. Mark products as paid if invoice is fully paid
        $this->markPaidProductsIfFullyPaid($invoice);

        Notification::make()
            ->title(__('Invoice created successfully'))
            ->success()
            ->send();

        $this->redirect(route('filament.admin.resources.invoices.view', $invoice));
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
                    $enrollment->update(['status_id' => 2]); // Paid
                }
            }
        }

        // Mark enrollments as paid
        foreach ($invoice->enrollments as $detail) {
            $enrollment = $detail->product;
            if ($enrollment instanceof Enrollment) {
                $enrollment->update(['status_id' => 2]); // Paid
            }
        }
    }
}
