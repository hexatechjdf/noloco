<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CsvMappingRequest extends FormRequest
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
            'title' => [
                'required',
                Rule::unique('csv_mappings', 'title')->ignore($this->route('id')), // Adjust route parameter as needed
            ],
            'unique_field' => 'required',
            // 'mapping' => 'required|array',
            // 'mapping.*' => 'required|string',
        ];
    }

     /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'unique_field.required' => 'The unique field is required.',
            'mapping.required' => 'The mapping data is required.',
            'mapping.*.required' => 'Each field must have a value selected.',
        ];
    }
}
