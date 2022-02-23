<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseCreateRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'rhythm_id' => 'required',
            'level_id' => 'nullable|integer',
            'name' => 'required|min:1|max:100',
            'price' => 'required|numeric|min:0',
            'volume' => 'nullable|numeric|min:0',
            'remote_volume' => 'nullable|numeric|min:0',
            'spots' => 'required|integer|min:0',
            'exempt_attendance' => 'nullable|boolean',
            'color' => 'nullable|string',
            'period_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'times.*.day' => 'required',
            'times.*.start' => 'required',
            'times.*.end' => 'required',
            'children.*.name' => 'required|min:1|max:100',
            'children.*.price' => 'required|numeric|min:0',
            'children.*.volume' => 'nullable|numeric|min:0',
            'children.*.remote_volume' => 'nullable|numeric|min:0',
            'children.*.start_date' => 'required|date',
            'children.*.end_date' => 'required|date',
        ];

        if (config('invoicing.price_categories_enabled')) {
            $rules['price_b'] = 'required|numeric|min:0';
            $rules['price_c'] = 'required|numeric|min:0';
        }

        return $rules;
    }
}
