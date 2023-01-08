<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnrollmentUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'course_id' => 'required',
            'total_price' => 'required|numeric|min:0',
            'status_id' => 'required|integer',
            'scheduledPayments.*.date' => 'required|date',
            'scheduledPayments.*.value' => 'required|numeric|min:0',
            'scheduledPayments.*.status' => 'required|in:1,2',
        ];
    }
}
