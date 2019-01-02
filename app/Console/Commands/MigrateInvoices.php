<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use  App\Models\PreInvoice;

class MigrateInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datamigration:invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve old prefacturas and convert them to the new system';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // retrieve the old prefacturas (detalle)
        $details = DB::table('afc2.bf_pre_factura_detalle')
        ->select(DB::raw('
            id,
            id_pre_factura_cabecera,
            id_matricula,
            v_detfac_descripcion,
            v_detfac_precio
            '))
        ->get();

        foreach ($details as $detail)
        {

            // if the price corresponds to a course
            if (
                $detail->v_detfac_precio == 140 ||
                $detail->v_detfac_precio == 215 ||
                $detail->v_detfac_precio == 365
                )
                {

                // substract the matricula fee from the total
                $new_product = new \App\Models\PreInvoiceDetail;
                $new_product->quantity = 1;
                $new_product->pre_invoice_id = $detail->id_pre_factura_cabecera;
                $new_product->product_name = $detail->v_detfac_descripcion;
                $new_product->price = $detail->v_detfac_precio - 20;
                $new_product->created_at = null;
                $new_product->updated_at = null;
                $new_product->save();

                // and add the matricula fee as a separate one
                $new_product = new \App\Models\PreInvoiceDetail;
                $new_product->quantity = 1;
                $new_product->pre_invoice_id = $detail->id_pre_factura_cabecera;
                $new_product->product_name = "COSTO MATRICULA";
                $new_product->price = 20;
                $new_product->created_at = null;
                $new_product->updated_at = null;
                $new_product->save();

            }

            else {
                        // otherwise just duplicate the record into the new table
                        $new_product = new \App\Models\PreInvoiceDetail;
                        $new_product->quantity = 1;
                        $new_product->pre_invoice_id = $detail->id_pre_factura_cabecera;
                        $new_product->product_name = $detail->v_detfac_descripcion;
                        $new_product->price = $detail->v_detfac_precio;
                        $new_product->created_at = null;
                        $new_product->updated_at = null;
                        $new_product->save();
            }
        }





        // try to retrieve the invoice numbers from the old comments

        $invoices = DB::table('afc2.bf_pre_factura_cabecera')
        ->select(DB::raw('
            id,
            observaciones
            '))
        ->get();

        foreach ($invoices as $invoice)
        {
        // parse the comment for an invoice number. Usually factura+XXXX
        $pattern = "/(?:factura ?|FACTURA ?)(\d+)/";
            if (preg_match($pattern, $invoice->observaciones, $match) == 1)
            { 
                // if a number is found, update the new pre_invoices table.
                $preinvoice = PreInvoice::findOrFail($invoice->id);
                $preinvoice->invoice_number = $match[1];
                $preinvoice->save();
            }
        }




        // create missing preinvoices for recent enrollments

        // retrieve the list of enrollments to migrate
        $enrollments = DB::table('afc2.enrollments')
        ->select(DB::raw('
            id
            '))
            ->where('id', '>', 2685)
        ->get();

        // for each enrollment

        // add the course to the cart

        
    }
}
