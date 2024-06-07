<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAIBotRequest extends FormRequest
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
        if (!$this->has('is_generated_photos')) {
            $this->merge(['is_generated_photos' => false]);
        }
        if (!$this->has('is_real_photos')) {
            $this->merge(['is_real_photos' => false]);
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
            'topic' => 'required|string',
            'keywords' => 'required|array',
            'prompt' => 'nullable|string',
            'post_template_id' => 'nullable|integer|exists:post_templates,id',
            'is_generated_photos' => 'nullable|boolean',
            'is_real_photos' => 'nullable|boolean',
            'post_planning' => 'nullable|array',
        ];
    }
}
