<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\ScheduledPayment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class fixIncorrectProductTypesCommand extends Command
{
    protected $signature = 'academico:fix-product-types';

    protected $description = 'Command description';

    public function handle()
    {

        foreach (Invoice::with('invoiceDetails')->get() as $invoice) {
            // Supprimer les factures qui ne correspondent à rien (souvent des doublons générés en cas d'erreurs dans la transmission à Ecuasolutions.
            if ($invoice->enrollments->count() > 0 && DB::table('enrollment_invoice')->where('invoice_id', $invoice->id)->count() === 0) {
                 DB::table('invoices')->where('id', $invoice->id)->delete();
                echo "\n facture # $invoice->id supprimée";
            }
        }

        // Vérifier que toutes les factures liées à une inscription ont bien le produit correspondant
        foreach (DB::table('enrollment_invoice')->get() as $enrollmentInvoicePair) {
            $invoice = Invoice::find($enrollmentInvoicePair->invoice_id);
            $enrollment = Enrollment::find($enrollmentInvoicePair->enrollment_id);
            if ($invoice && $enrollment) {
                if ($invoice->enrollments->where('product_id', $enrollment->id)->count() === 0) {
                    $invoiceDetail = InvoiceDetail::where(['invoice_id' => $invoice->id, 'price' => $enrollment->price * 100])->first();

                    if ($invoiceDetail) {
                        $invoiceDetail->update([
                            'product_name' => 'Inscription # ' . $enrollment->id,
                            'product_code' => '',
                            'product_id' => $enrollment->id,
                            'product_type' => Enrollment::class,
                            'quantity' => 1,
                        ]);
                        echo "\n inscription # $enrollment->id mise à jour dans la facture # $invoice->id";
                    } else {
                        InvoiceDetail::create([
                            'invoice_id' => $invoice->id,
                            'product_name' => 'Inscription # ' . $enrollment->id,
                            'product_code' => '',
                            'product_id' => $enrollment->id,
                            'product_type' => Enrollment::class,
                            'quantity' => 1,
                            'price' => 0,
                        ]);
                        echo "\n Produit vide généré pour lier l'inscription # $enrollment->id à la facture # $invoice->id";
                    }

                }
            }
        }
    }
}
