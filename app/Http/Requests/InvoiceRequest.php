<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

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
//            'payments'      => function ($attribute, $value, $fail) {
//                $fieldGroups = json_decode($value);
//
//                // allow repeatable field to be empty
//                if (count($fieldGroups) == 0) {
//                    return true;
//                }
//
//                // SECOND-LEVEL REPEATABLE VALIDATION
//                // run through each field group inside the repeatable field
//                // and run a custom validation for it
//                foreach ($fieldGroups as $key => $group) {
//                    $fieldGroupValidator = Validator::make((array) $group, ['payment_method' => 'required|string', 'date' => 'required|date', 'value' => 'numeric|required', 'comment' => 'string|nullable', 'responsable_id' => 'nullable']);
//
//                    if ($fieldGroupValidator->fails()) {
//                        return $fail($fieldGroupValidator->errors()->first());
//                    }
//                }
//            },
//            'invoiceDetails'      => function ($attribute, $value, $fail) {
//                $fieldGroups = json_decode($value);
//
//                // allow repeatable field to be empty
//                if (count($fieldGroups) == 0) {
//                    return true;
//                }
//
//                // SECOND-LEVEL REPEATABLE VALIDATION
//                // run through each field group inside the repeatable field
//                // and run a custom validation for it
//                foreach ($fieldGroups as $key => $group) {
//                    $fieldGroupValidator = Validator::make((array) $group, ['product_name' => 'required|string', 'price' => 'numeric|required', 'quantity' => 'numeric|required']);
//
//                    if ($fieldGroupValidator->fails()) {
//                        return $fail($fieldGroupValidator->errors()->first());
//                    }
//                }
//            },
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
