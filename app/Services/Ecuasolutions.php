<?php

namespace App\Services;

use App\Interfaces\InvoicingInterface;
use App\Models\Book;
use App\Models\Enrollment;
use App\Models\Fee;
use App\Models\Invoice;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Ecuasolutions implements InvoicingInterface
{
    public function status() : bool
    {
        // TODO : need to find another solution to ping the server before making the request
        return true;
    }

    public function saveInvoice(Invoice $invoice) : ?string
    {
        $ivkardex = [];
        $pckardex = [];

        foreach ($invoice->payments as $p => $payment) {
            $pckardex[$p] = [
                'codforma' => $payment->payment_method,
                'valor' => $payment->value,
                'fechaemision' => $invoice->created_at,
                'fechavenci' => $invoice->created_at,
                'observacion' => $payment->comment,
                'codprovcli' => $invoice->client_idnumber,
            ];
        }

        foreach ($invoice->invoiceDetails as $p => $product) {
            if ($product->product instanceof Enrollment)
            {   
                $ivkardex[$p] = [
                    'codinventario' => $product->product_code,
                    'codbodega' => 'MAT',
                    'cantidad' => 1,
                    'descuento' => 0,
                    'iva' => 0,
                    'preciototal' => $product->final_price,
                    'valoriva' => 0,
                ];
            }
            elseif ($product->product instanceof Fee)
            {
                $ivkardex[$p] = [
                    'codinventario' => $product->product_code,
                    'codbodega' => 'MAT',
                    'cantidad' => 1,
                    'descuento' => 0,
                    'iva' => 0,
                    'preciototal' => $product->final_price,
                    'valoriva' => 0,
                ];
            }
            elseif ($product->product instanceof Book)
            {
                $ivkardex[$p] = [
                    'codinventario' => $product->product_code,
                    'codbodega' => 'MAT',
                    'cantidad' => 1,
                    'descuento' => 0,
                    'iva' => 0,
                    'preciototal' => $product->final_price,
                    'valoriva' => 0,
                ];
            }
        }

        $body = [
            'codtrans' => 'FE', // was 'OP'
            'numtrans' => $invoice->id,
            'fechatrans' => $invoice->created_at,
            'horatrans' => $invoice->created_at,
            'descripcion' => 'Facturado desde el academico por '.backpack_user()->firstname.' '.backpack_user()->lastname,
            'codusuario' => 'web',
            'codprovcli' => $invoice->client_idnumber, // si existe, se busca el cliente. Si no lo creamos.
            'nombre' => $invoice->client_name,
            'direccion' => $invoice->client_address,
            'telefono' => $invoice->client_phone,
            'email' => $invoice->client_email,
            'codvendedor' => '',
            'ivkardex' => $ivkardex,
            'pckardex' => $pckardex,
        ];

        $client = new Client(['debug' => true, 'connect_timeout' => 20]);

        $serverurl = config('invoicing.ecuasolutions.url');

        $response = $client->post($serverurl, [
            'headers' => [
                'authorization' => config('invoicing.ecuasolutions.key'),
                'Content-Type' => 'application/json',
            ],
            'json' => $body,
        ]);

        Log::info('Sending data to accounting');

        if ($response->getBody()) {
            $code = json_decode(preg_replace('/[\\x00-\\x1F\\x80-\\xFF]/', '', $response->getBody()), true);
        }

        Log::info($response->getBody());

        return isset($code['mensaje']) ? $code['mensaje'] : null;
    }
}
