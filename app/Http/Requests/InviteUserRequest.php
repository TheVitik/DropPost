<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InviteUserRequest extends FormRequest
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
        // TODO: Except auth user fields
        return [
            'user_id' => 'required_without_all:telegram_user_id,username|exists:users,id',
            'telegram_user_id' => 'required_without_all:user_id,username|numeric',
            'username' => 'required_without_all:user_id,telegram_user_id|alpha_dash',
        ];
    }
}
