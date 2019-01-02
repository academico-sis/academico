<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\PreInvoice;
use App\Models\PreInvoiceDetail;
use App\Models\User;

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
                $new_product->pre_invoice_id = $detail->id_pre_factura_cabecera;
                $new_product->product_name = $detail->v_detfac_descripcion;
                $new_product->price = $detail->v_detfac_precio - 20;
                $new_product->created_at = null;
                $new_product->updated_at = null;
                $new_product->save();

                // and add the matricula fee as a separate one
                $new_product = new \App\Models\PreInvoiceDetail;
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
            enrollments.id, enrollments.id_user as id_user, enrollments.fecha as fecha,
            enrollments.id_factura as invoice_number,
            courses.name as course_name, courses.price as course_price
            '))
            ->join('courses', 'id_cursos', 'courses.id')
            ->where('enrollments.id', '>', 2684)
        ->get();

        //dd($enrollments);
        // for each enrollment
        foreach ($enrollments as $enrollment)
        {
            // generate an invoice for the course + a matricula fee

        $client = User::find($enrollment->id_user);

        // generate a new preinvoice
        $preinvoice = new PreInvoice;
        $preinvoice->user_id = $enrollment->id_user;
        //$preinvoice->user_data_id = $invoice_data->id;
        $preinvoice->client_name = $client->name;
        $preinvoice->client_idnumber =  $client->idnumber;
        $preinvoice->client_address = $client->address;
        $preinvoice->client_email = $client->email;
        $preinvoice->total_price = 0;
        $preinvoice->invoice_number = $enrollment->invoice_number;

        $preinvoice->save();

        echo "generated preinvoice\n";

        // generate a preinvoice product (detail)
        $detail = new PreInvoiceDetail;
        $detail->pre_invoice_id = $preinvoice->id;
        $detail->product_name = $enrollment->course_name;
        $detail->price = $enrollment->course_price;
        $detail->created_at = $enrollment->fecha;
        $detail->save();

        $matricula = new PreInvoiceDetail;
        $matricula->pre_invoice_id = $preinvoice->id;
        $matricula->product_name = "COSTO MATRICULA";
        $matricula->price = 20;
        $matricula->created_at = $enrollment->fecha;
        $matricula->save();

        }

        }


        
    }

