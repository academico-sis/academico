<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Config;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PreInvoiceDetail;
use GuzzleHttp\Client;
use Http\Client\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Ecuasolutions
{
    public function checkServerStatus()
    {
        $server = DB::table('monitors')->where('url', config('services.ecuasolutions.pingurl'))->first();
        if ($server) {
            return $server->uptime_status == 'up' ? true : false;
        } else {
            return false;
        }
    }

    protected function saveInvoiceData($request, $preinvoice)
    {

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
    }

    /** This class will be called during the enrollment checkout process to transmit data to an external accounting system.
     * Other implementations may be created in the future (in this case we'll need to bind this class in the service container).
     */
    public function sendInvoiceToAccountingSystem($request, $preinvoice)
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

        $client = new Client(['debug' => true, 'connect_timeout' => 20]);

        $serverurl = Config::where('name', 'ACCOUNTING_URL')->first()->value;

        try {
            $response = $client->post($serverurl, [
                'headers' => [
                    'authorization' => Config::where('name', 'ACCOUNTING_TOKEN')->first()->value,
                    'Content-Type' => 'application/json',
                ],
                'json' => $body,
            ]);

            Log::info('Sending data to accounting');
            Log::info(print_r($response));

            if ($response->getBody()) {
                $code = json_decode(preg_replace('/[\\x00-\\x1F\\x80-\\xFF]/', '', $response->getBody()), true);
            }

            if ($code['mensaje'] !== null) {
                $preinvoice->invoice_number = $code['mensaje'];
                $preinvoice->save();

                $this->saveInvoiceData($request, $preinvoice);
            } else {
                abort(422);
            }
        } catch (Exception $exception) {
            Log::error('Data could not be sent to accounting');
            Log::error($exception);
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
}
