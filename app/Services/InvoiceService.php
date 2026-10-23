<?php

namespace App\Services;

use App\Mail\InvoiceBulkDownload;
use App\Models\Config;
use App\Models\Enrollment;
use App\Models\Invoice;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice as LaravelDailyInvoice;

class InvoiceService
{
    public function download(Invoice $invoice): LaravelDailyInvoice
    {
        App::setLocale(config('app.locale'));

        $customer = new Buyer([
            'name' => $invoice->client_name,
            'address' => $invoice->client_address,
            'vat' => $invoice->client_idnumber,
            'custom_fields' => array_filter([
                __('Email') => $invoice->client_email,
            ]),
        ]);

        $seller = new Party([
            'name' => config('academico.company_name'),
            'address' => config('academico.company_address'),
            'vat' => config('academico.company_id'),
            'phone' => config('academico.company_phone'),
        ]);

        $notes = [$invoice->invoiceType->notes];

        foreach ($invoice->comments as $comment) {
            $notes[] = $comment->body;
        }

        $notes = implode('<br><br>', $notes);

        $currencyFormat = config('academico.currency_position') === 'before' ? '{SYMBOL}{VALUE}' : '{VALUE}{SYMBOL}';
        $generatedInvoice = LaravelDailyInvoice::make()
            ->seller($seller)
            ->buyer($customer)
            ->series($invoice->invoice_series)
            ->sequence($invoice->invoice_number ?? $invoice->id)
            ->dateFormat('d/m/Y')
            ->date($invoice->date);

        $generatedInvoice
            ->currencySymbol(config('academico.currency_symbol'))
            ->currencyCode(config('academico.currency_code'))
            ->currencyFormat($currencyFormat)
            ->notes($notes ?? '');

        if (file_exists(storage_path('logo2.png'))) {
            $generatedInvoice->logo(storage_path('logo2.png'));
        }

        foreach ($invoice->invoiceDetails as $product) {
            $item = (new InvoiceItem)->title($product->product_name)->pricePerUnit($product->price)->quantity($product->quantity);

            $description = null;

            if ($product->product instanceof Enrollment) {
                if ($product->product->course->name) {
                    $description .= __('Course').': '.$product->product->course->name;
                    $description .= '<br>'.__('Period').': '.$product->product->course->period->name;
                }

                if ($product->product->course?->level?->name) {
                    $description .= '<br>'.__('Level').': '.$product->product->course->level->name;
                }
            }

            if ($product->comment) {
                $description .= '<br><br>'.$product->comment;
            }

            if ($description) {
                $item->description($description);
            }

            $generatedInvoice->addItem($item);
        }

        $generatedInvoice->footer = Config::firstWhere('name', 'invoice_footer')->value ?? '';

        return $generatedInvoice;
    }

    public function sendFileByEmail(string $filename, string $email): void
    {
        Mail::to($email)
            ->send(new InvoiceBulkDownload($filename));
    }
}
