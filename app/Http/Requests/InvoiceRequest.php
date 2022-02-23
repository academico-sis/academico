<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'client_name' => 'required',
            'client_idnumber' => 'required',
            'client_address' => 'required',
            'client_email' => 'required',
            'payments.*.payment_method' => 'required|string',
            'payments.*.date' => 'required|date',
            'payments.*.value' => 'numeric|required',
            'payments.*.comment' => 'string|nullable',
            'invoiceDetails.*.product_name' => 'required|string',
            'invoiceDetails.*.price' => 'numeric|required',
            'invoiceDetails.*.quantity' => 'numeric|required',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
