<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_image' => ['required', 'image', 'mimes:jpeg,png'],
            'name' => ['required', 'string', 'max:20'],
            'zip' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'profile_image.image' => 'プロフィール画像は画像ファイルを選択してください。',
            'profile_image.mimes' => 'プロフィール画像は.jpegもしくは.png形式でアップロードしてください。',

            'name.required' => 'ユーザー名を入力してください。',
            'name.max' => 'ユーザー名は20文字以内で入力してください。',

            'zip.required' => '郵便番号を入力してください。',
            'zip.regex' => '郵便番号はハイフンありの8文字で入力してください。',

            'address.required' => '住所を入力してください。',
            'address.max' => '住所は255文字以内で入力してください。',

            'building.max' => '建物名等は255文字以内で入力してください。',
        ];
    }
}