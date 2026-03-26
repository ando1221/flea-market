<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method_id' => [
                'required',
                'integer',
                Rule::exists('payment_methods', 'id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method_id.required' => '支払い方法を選択してください。',
            'payment_method_id.integer' => '支払い方法を正しく選択してください。',
            'payment_method_id.exists' => '支払い方法を正しく選択してください。',
        ];
    }
}