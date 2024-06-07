<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TelegramLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
            'first_name' => ['required', 'string'],
            'last_name' => ['nullable', 'string'],
            'photo_url' => ['nullable', 'string'],
            'username' => ['nullable', 'string'],
            'hash' => ['required', 'string'],
            'auth_date' => ['required', 'integer'],
        ];
    }
}
