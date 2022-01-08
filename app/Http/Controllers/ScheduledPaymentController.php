<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Discount;
use App\Models\Enrollment;
use App\Models\Fee;
use App\Models\InvoiceType;
use App\Models\Paymentmethod;
use App\Models\ScheduledPayment;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScheduledPaymentController extends Controller
{
    public function create(Enrollment $enrollment)
    {
        return view('invoices.create_scheduled_payments', [
            'enrollment' => $enrollment,
        ]);
    }

    public function store(Enrollment $enrollment, Request $request)
    {
        // Should only be used to create payments, not to edit them.
        foreach ($request->payments as $p => $payment) {
            $enrollment->scheduledPayments()->create([
                'responsable_id' => backpack_user()->id,
                'value' => $payment['value'],
                'date' => $payment['date'],
                'status' => 1,
            ]);
        }
    }

    /**
     * Create a new cart with the specified payment
     * and display the cart.
     */
    public function bill(ScheduledPayment $scheduledPayment)
    {
        // otherwise create a new one.
        Log::info('User # '.backpack_user()->id.' is generating a invoice for a scheduled payment');

        // build an array with products to include
        $products = [];

        $enrollment = $scheduledPayment->enrollment;

        array_push($products, [
            'name' => $enrollment->name,
            'product_code' => $enrollment->product_code,
            'type' => 'scheduledPayment',
            'price' => $scheduledPayment->value,
            'quantity' => 1,
            'id' => $scheduledPayment->id,
        ]);

        // build an array with all contact data
        $clients = [];

        array_push($clients, [
            'name' => $enrollment->student_name,
            'email' => $enrollment->student_email,
            'idnumber' => $enrollment->student->idnumber,
            'address' => $enrollment->student->address,
            'phone' => $enrollment->student->phone,
        ]);

        foreach ($enrollment->student->contacts as $client) {
            array_push($clients, $client);
        }

        $data = [
            'enrollment' => $enrollment,
            'products' => $products,
            'invoicetypes' => InvoiceType::all(),
            'clients' => $clients,
            'availableBooks' => Book::all(),
            'availableFees' => Fee::all(),
            'availableDiscounts' => Discount::all(),
            'availablePaymentMethods' => Paymentmethod::all(),
            'availableTaxes' => Tax::all(),
        ];
        if (config('invoicing.price_categories_enabled')) {
            $data = array_merge(
                $data,
                [
                    'priceCategories' => collect([
                        'price_a' => $enrollment->course->price,
                        'price_b' => $enrollment->course->price_b,
                        'price_c' => $enrollment->course->price_c,
                    ]),
                    'studentPriceCategory' => $enrollment->student?->price_category,
                ]
            );
        }

        return view('carts.show', $data);
    }
}
