<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Services\InvoiceService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_pdf')
                ->label(__('Download PDF'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->action(function () {
                    $service = app(InvoiceService::class);
                    $record = $this->getRecord();

                    return response()->streamDownload(function () use ($service, $record) {
                        echo $service->download($record)->stream()->getContent();
                    }, 'invoice-'.($record->invoice_reference ?? $record->id).'.pdf');
                }),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
