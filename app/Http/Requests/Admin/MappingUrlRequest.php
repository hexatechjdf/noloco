<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MappingUrlRequest extends FormRequest
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
    public function rules()
    {
        return [
            'searchable_fields' => 'required|array|min:1',
            'searchable_fields.*' => 'required|string',

            'displayable_fields' => 'required|array|min:1',
            'displayable_fields.*' => 'required|string',
            'maps' => 'required|array|min:1',
            'maps.*' => 'required|string',
            'attr' => 'required|array|min:1',
            'attr.*' => 'required|string',
            'table' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'searchable_fields.required' => 'Searchable fields are required.',
            'searchable_fields.*.required' => 'Each searchable field is required.',
            'displayable_fields.required' => 'Displayable fields are required.',
            'displayable_fields.*.required' => 'Each displayable field is required.',
            'maps.required' => 'Mapping are required.',
            'maps.*.required' => 'Each mapping field is required.',
            'attr.required' => 'Attributes are required.',
            'attr.*.required' => 'Each attribute field is required.',
            'related_urls.*.required_with' => 'Each related URL is required when related URLs are provided.',
        ];
    }
}
