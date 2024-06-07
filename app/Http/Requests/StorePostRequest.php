<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
        // bool
        $this->merge([
            'is_advertisement' => $this->is_advertisement === 'true',
            'is_draft' => $this->is_draft === 'true',
            'is_template' => $this->is_template === 'true',
        ]);

        if($this->has('plan_publish_date')) {
            $this->merge([
                'plan_publish_date' => Carbon::parse($this->plan_publish_date),
            ]);
        }
        if ($this->has('plan_delete_date')) {
            $this->merge([
                'plan_delete_date' => Carbon::parse($this->plan_delete_date),
            ]);
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
            'channels' => 'required|array',
            'channels.*' => 'required',
            'content_html' => 'nullable|string',
            'plan_publish_date' => 'nullable|date',
            'plan_delete_date' => 'nullable|date',
            'is_advertisement' => 'required|boolean',
            'is_draft' => 'required|boolean',
            'is_template' => 'required|boolean',
            'files' => 'nullable|array',
            'files.*' => 'required|file',
        ];
    }
}
