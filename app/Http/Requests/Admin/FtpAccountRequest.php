<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\StrongPassword;

class FtpAccountRequest extends FormRequest
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
            'username' => 'required|string|max:255',
            // 'location_id' => 'required',
            // 'domain' => 'required|string',
            // 'password' => ['required', 'string', new StrongPassword()],
            // 'quota' => 'required|in:unlimited,limited',
            // 'quota_value' => 'nullable|integer|min:1', // Only if 'quota' is limited
            // 'directory' => 'required|string',
        ];
    }

     /**
     * Custom validation messages (optional).
     */
    public function messages(): array
    {
        return [
            'logusernamein.required' => 'Username is required.',
            'domain.required' => 'Please select a domain.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Passwords do not match.',
            'quota.required' => 'Quota is required.',
            'quota_value.min' => 'Quota must be at least 1 MB.',
            'directory.required' => 'Directory is required.',
        ];
    }
}
