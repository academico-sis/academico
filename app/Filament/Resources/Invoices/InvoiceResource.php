<?php

namespace App\Filament\Resources\Invoices;

use App\Filament\Resources\Invoices\Pages\EditInvoice;
use App\Filament\Resources\Invoices\Pages\ListInvoices;
use App\Filament\Resources\Invoices\Pages\ViewInvoice;
use App\Filament\Resources\Invoices\RelationManagers\InvoiceDetailsRelationManager;
use App\Filament\Resources\Invoices\RelationManagers\PaymentsRelationManager;
use App\Models\Invoice;
use App\Services\InvoiceService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?int $navigationSort = 380;

    public static function canAccess(): bool
    {
        return config('invoicing.accounting_enabled') && ! config('invoicing.price_categories_enabled');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Accounting');
    }

    public static function getModelLabel(): string
    {
        return __('Invoice');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Invoices');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['invoiceType', 'payments', 'invoiceDetails']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->label(__('Date'))
                    ->required(),
                Select::make('invoice_type_id')
                    ->relationship('invoiceType', 'name')
                    ->label(__('Invoice Type'))
                    ->preload()
                    ->required(),
                TextInput::make('invoice_number')
                    ->label(__('Invoice Number'))
                    ->numeric()
                    ->visible(fn () => config('invoicing.invoice_numbering') !== 'manual'),
                TextInput::make('receipt_number')
                    ->label(__('Receipt Number'))
                    ->visible(fn () => config('invoicing.invoice_numbering') === 'manual'),
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Mobile: stacked invoice info
                TextColumn::make('mobile_invoice')
                    ->label(__('Invoice'))
                    ->state(fn ($record) => $record->invoice_reference.' · '.$record->invoiceType?->name)
                    ->description(fn ($record) => $record->client_name.' · '.$record->date?->format('M j, Y'))
                    ->searchable(query: fn ($query, $search) => $query->where('client_name', 'like', "%{$search}%")->orWhere('invoice_number', 'like', "%{$search}%"))
                    ->wrap()
                    ->hiddenFrom('md'),
                // Desktop columns
                TextColumn::make('invoice_reference')
                    ->label(__('Invoice #'))
                    ->searchable(['invoice_number', 'receipt_number'])
                    ->sortable('invoice_number')
                    ->visibleFrom('md'),
                TextColumn::make('invoiceType.name')
                    ->label(__('Type'))
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('date')
                    ->label(__('Date'))
                    ->date()
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('client_name')
                    ->label(__('Client'))
                    ->wrap()
                    ->width('160px')
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('client_idnumber')
                    ->label(__('ID Number'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visibleFrom('lg'),
                TextColumn::make('client_email')
                    ->label(__('Email'))
                    ->wrap()
                    ->width('180px')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visibleFrom('lg'),
                TextColumn::make('total_price_with_currency')
                    ->label(__('Total'))
                    ->sortable(false),
                TextColumn::make('balance')
                    ->label(__('Balance'))
                    ->sortable(false)
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->formatStateUsing(function ($state) {
                        $symbol = config('academico.currency_symbol');
                        $position = config('academico.currency_position');

                        return $position === 'before'
                            ? $symbol.' '.number_format($state, 2)
                            : number_format($state, 2).' '.$symbol;
                    }),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Filter::make('date')
                    ->form([
                        DatePicker::make('from')->label(__('From')),
                        DatePicker::make('until')->label(__('Until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('date', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('date', '<=', $date));
                    }),
                SelectFilter::make('invoice_type_id')
                    ->relationship('invoiceType', 'name')
                    ->label(__('Invoice Type'))
                    ->preload(),
            ])
            ->recordActions([
                Action::make('download_pdf')
                    ->label(__('PDF'))
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->action(function (Invoice $record) {
                        $service = app(InvoiceService::class);

                        return response()->streamDownload(function () use ($service, $record) {
                            echo $service->download($record)->stream()->getContent();
                        }, 'invoice-'.($record->invoice_reference ?? $record->id).'.pdf');
                    }),
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            InvoiceDetailsRelationManager::class,
            PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoices::route('/'),
            'view' => ViewInvoice::route('/{record}'),
            'edit' => EditInvoice::route('/{record}/edit'),
        ];
    }
}
