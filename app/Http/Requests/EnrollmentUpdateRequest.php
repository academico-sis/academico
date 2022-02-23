<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnrollmentUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'course_id' => 'required',
            'price' => 'required|numeric|min:0',
            'status_id' => 'required|integer',
            'scheduledPayments.*.date' => 'required|date',
            'scheduledPayments.*.value' => 'required|numeric|min:0',
            'scheduledPayments.*.status' => 'required|in:1,2',

        ];

        if (config('invoicing.price_categories_enabled')) {
            $rules['price_b'] = 'required|numeric|min:0';
            $rules['price_c'] = 'required|numeric|min:0';
        }

        return $rules;
    }
}
