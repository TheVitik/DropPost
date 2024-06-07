<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetAIBotRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // if ai_bot_id is empty, set it to null
        if (empty($this->ai_bot_id)) {
            $this->merge(['ai_bot_id' => null]);
        }
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ai_bot_id' => ['nullable','exists:ai_bots,id'],
        ];
    }
}
