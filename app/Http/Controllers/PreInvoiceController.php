<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

use App\Models\Fee;
use App\Models\Book;
use App\Models\Cart;
use App\Models\User;
use App\Models\Course;
use App\Models\Contact;

use App\Models\Enrollment;
use App\Models\PreInvoice;
use Illuminate\Http\Request;
use App\Models\PreInvoiceDetail;

class PreInvoiceController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:enrollments.edit']);
    }
    
    /**
     * Create a preinvoice based on the cart contents for the specified user
     * Receive in the request: the user ID + the invoice data.
     */
    public function store(Request $request)
    {
        
        $ivkardex = [];
        $pckardex = [];

        // receive the client data and generate the preinvoice with status = pending

        $preinvoice = PreInvoice::create([
            'client_name' => $request->client_name,
            'client_idnumber' => $request->client_idnumber,
            'client_address' => $request->client_address,
            'client_email' => $request->client_email,
            'total_price' => $request->total_price,
        ]);

        // receive the list of products and generate the preinvoice details

        foreach($request->enrollments as $e => $enrollment)
        {
            $enrollment = Enrollment::find($enrollment['id']);

            PreInvoiceDetail::create([
                'pre_invoice_id' => $preinvoice->id,
                'product_name' => $enrollment['course']['name'], // todo
                'product_code' => $enrollment['course']['product_code'],
                'product_id' => $enrollment['id'],
                'product_type' => Enrollment::class,
                'price' => $enrollment['course']['price']
            ]);

            $preinvoice->enrollments()->attach($enrollment);

        }

        foreach($request->fees as $f => $fee)
        {
            PreInvoiceDetail::create([
                'pre_invoice_id' => $preinvoice->id,
                'product_name' => $fee['name'],
                'product_code' => $fee['product_code'],
                'product_id' => $fee['id'],
                'product_type' => Fee::class,
                'price' => $fee['price']
            ]);
        }

        foreach($request->books as $b => $book)
        {
            PreInvoiceDetail::create([
                'pre_invoice_id' => $preinvoice->id,
                'product_name' => $book['name'],
                'product_code' => $book['product_code'],
                'product_id' => $book['id'],
                'product_type' => Book::class,
                'price' => $book['price']
            ]);
        }


        // send the details to Accounting


        foreach($request->payments as $p => $payment)
        {
            
            $pckardex[$p] = [
            "codforma" => $payment['method'],
            "valor" => $payment['value'],
            "fechaemision" => $preinvoice->created_at,
            "fechavenci" => $preinvoice->created_at,
            "observacion" => $payment['comment'],
            "codprovcli" => "1790017478001" // todo
            ];

        }

        foreach($request->products as $p => $product)
        {
            $ivkardex[$p] = [
                "codinventario" => $product['codinventario'],
                "codbodega" => "MAT",
                "cantidad" => 1,
                "descuento" => $product['descuento'],
                "iva" => 0.12,
                "preciototal" => $product['preciototal'],
                "valoriva" => $product['preciototal'] * 0.12
            ];

        }



        $response = [
            "codtrans" => "OP", // ?
            "numtrans" => $preinvoice->id,
            "fechatrans" => $preinvoice->created_at,
            "horatrans" => $preinvoice->created_at,
            "descripcion" => "Factura generada desda la plataforma academica",
            "codusuario" => backpack_user()->firstname . " " . backpack_user()->lastname, 
            "codprovcli" => $preinvoice->client_idnumber, // si existe, se busca el cliente. Si no lo creamos.
            "nombre" => $preinvoice->client_name,
            "direccion" => $preinvoice->client_address,
            "telefono" => "", // TODO
            "email" => $preinvoice->client_email,
            "codvendedor" => "", // cual es la diferencia con codusuario?
            "ivkardex" => $ivkardex,
            "pckardex" => $pckardex,
        ];

        $client = new Client();
        
        $serverurl = 'http://192.168.100.12:81/api/v1/grabarpedido';
        
        $response = $client->post($serverurl, [
            'debug' => TRUE,
            'headers' => [
                'authorization' => 'SmFSby4yMDE3',
                'Content-Type' => 'application/json'
            ],
            'json' => $response,
            
          ]);

        // receive the confirmation

        // mark the preinvoice and associated enrollments as paid.
        foreach($preinvoice->enrollments as $enrollment)
        {
            //$enrollment->markAsPaid();
        }

        // show a confirmation
    }




    /**
     * Update the specified preinvoice (with the invoice number).
     */
    public function update(Request $request, PreInvoice $preInvoice)
    {
        $preInvoice->invoice_number = $request->input('invoice_number');
        $preInvoice->save();
        Alert::success(__('The invoice number has been saved'))->flash();

        return redirect()->back();
    }

}
