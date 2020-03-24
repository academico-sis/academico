<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Book;
use App\Models\User;
use App\Models\Config;
use App\Models\Course;
use GuzzleHttp\Client;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Payment;
use App\Models\Enrollment;
use App\Models\PreInvoice;
use Illuminate\Http\Request;
use App\Models\PreInvoiceDetail;
use Prologue\Alerts\Facades\Alert;
use GuzzleHttp\Exception\GuzzleException;

class PreInvoiceController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:enrollments.edit']);
    }

    /** This part is optionnal. It can be disabled system-wide (config table) or per-operation during the enrollment checkout process.
     * In the future, this should be implemented using an interface to allow integration with other accounting systems
     */
    protected function sendInvoiceToAccountingSystem($request, $preinvoice)
    {
        foreach ($request->payments as $p => $payment) {
            $pckardex[$p] = [
                'codforma' => $payment['method'],
                'valor' => $payment['value'],
                'fechaemision' => $preinvoice->created_at,
                'fechavenci' => $preinvoice->created_at,
                'observacion' => $payment['comment'],
                'codprovcli' => $request->client_idnumber,
            ];
        }

        foreach ($request->products as $p => $product) {
            $ivkardex[$p] = [
                'codinventario' => $product['codinventario'],
                'codbodega' => 'MAT',
                'cantidad' => 1,
                'descuento' => $product['descuento'],
                'iva' => 0,
                'preciototal' => $product['preciototal'],
                'valoriva' => 0,
            ];
        }

        $body = [
            'codtrans' => 'FE', // was 'OP'
            'numtrans' => $preinvoice->id,
            'fechatrans' => $preinvoice->created_at,
            'horatrans' => $preinvoice->created_at,
            'descripcion' => 'Facturado desde el academico por '.backpack_user()->firstname.' '.backpack_user()->lastname,
            'codusuario' => 'web',
            'codprovcli' => $preinvoice->client_idnumber, // si existe, se busca el cliente. Si no lo creamos.
            'nombre' => $preinvoice->client_name,
            'direccion' => $preinvoice->client_address,
            'telefono' => $preinvoice->client_phone,
            'email' => $preinvoice->client_email,
            'codvendedor' => '',
            'ivkardex' => $ivkardex,
            'pckardex' => $pckardex,
        ];

        $client = new Client();

        $serverurl = Config::where('name', 'ACCOUNTING_URL')->first()->value;

        try {
            $response = $client->post($serverurl, [
                'headers' => [
                    'authorization' => Config::where('name', 'ACCOUNTING_TOKEN')->first()->value,
                    'Content-Type' => 'application/json',
                ],
                'json' => $body,
              ]);

            if ($response->getBody()) {
                $code = json_decode(preg_replace('/[\\x00-\\x1F\\x80-\\xFF]/', '', $response->getBody()), true);
            }

            $preinvoice->invoice_number = $code['mensaje'];
            $preinvoice->save();
        } catch (Exception $exception) {
            parent::report($exception);

            foreach ($preinvoice->pre_invoice_details as $product) {
                $product->destroy();
            }

            foreach ($preinvoice->payments as $payment) {
                $payment->destroy();
            }

            $preinvoice->destroy();
        }
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
            'client_phone' => $request->client_phone,
            'total_price' => $request->total_price,
        ]);

        // receive the list of products and generate the preinvoice details

        foreach ($request->enrollments as $e => $enrollment) {
            $enrollment = Enrollment::find($enrollment['id']);
            if ($enrollment->status_id != 1) {
                abort(422, 'Esta matricula no esta pendiente');
            }
            
            PreInvoiceDetail::create([
                'pre_invoice_id' => $preinvoice->id,
                'product_name' => $enrollment['course']['name'],
                'product_code' => $enrollment['course']['product_code'],
                'product_id' => $enrollment['id'],
                'product_type' => Enrollment::class,
                'price' => $enrollment['course']['price'],
            ]);

            $preinvoice->enrollments()->attach($enrollment);

            if (isset($request->comment)) {
                Comment::create([
                    'commentable_id' => $enrollment->id,
                    'commentable_type' => Enrollment::class,
                    'body' => $request->comment,
                    'author_id' => backpack_user()->id,
                ]);
            }
        }

        foreach ($request->fees as $f => $fee) {
            PreInvoiceDetail::create([
                'pre_invoice_id' => $preinvoice->id,
                'product_name' => $fee['name'],
                'product_code' => $fee['product_code'],
                'product_id' => $fee['id'],
                'product_type' => Fee::class,
                'price' => $fee['price'],
            ]);
        }

        foreach ($request->books as $b => $book) {
            PreInvoiceDetail::create([
                'pre_invoice_id' => $preinvoice->id,
                'product_name' => $book['name'],
                'product_code' => $book['product_code'],
                'product_id' => $book['id'],
                'product_type' => Book::class,
                'price' => $book['price'],
            ]);
        }


        foreach ($request->payments as $p => $payment) {
            Payment::create([
                'responsable_id' => backpack_user()->id,
                'pre_invoice_id' => $preinvoice->id,
                'payment_method' => $payment['method'],
                'value' => $payment['value'],
                'comment' => $payment['comment'],
            ]);
        }

        // send the details to Accounting
        // if the user selected so
        // and receive and store the invoice number
        if ($request->sendinvoice == true && config('settings.external_accounting_enabled') == true) {
            $this->sendInvoiceToAccountingSystem($request, $preinvoice);
        }

        // mark the preinvoice and associated enrollments as paid.
        foreach ($preinvoice->enrollments as $enrollment) {
            $enrollment->markAsPaid();
        }
    }

    public function edit(PreInvoice $preInvoice)
    {
        return view('invoices.edit', compact('preInvoice'));
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
