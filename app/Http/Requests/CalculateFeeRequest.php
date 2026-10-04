<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculateFeeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'dehyari_id'    => 'required|exists:dehyaris,id',
            'fee_type_id'   => 'required|exists:fee_types,id',
            'taxpayer_name' => 'required|string|max:255|min:3',
            'national_code' => 'nullable|string|regex:/^[0-9]{10}$/',
            'mobile'        => 'required|string|regex:/^09[0-9]{9}$/',
            'address'       => 'nullable|string|max:500',
            'inputs'        => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.regex'        => 'شماره موبایل باید با ۰۹ شروع شده و ۱۱ رقم باشد.',
            'national_code.regex' => 'کد ملی باید ۱۰ رقم عددی باشد.',
        ];
    }
}
