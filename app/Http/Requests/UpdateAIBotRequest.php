<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAIBotRequest extends FormRequest
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
            'topic' => ['required', 'string'],
            'keywords' => ['required', 'string'],
            'prompt' => ['required', 'string'],
            'post_template_id' => ['required', 'integer', 'exists:post_templates,id'],
            'is_generated_photos' => ['required', 'boolean'],
            'is_real_photos' => ['required', 'boolean'],
            'post_planning' => ['required', 'string'],
        ];
    }
}
