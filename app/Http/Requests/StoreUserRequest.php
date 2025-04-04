<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
         'name' => 'required|string|max:255',
         'email' => 'required|email|unique:users,email',
         'phone_number' => 'required|string|max:15',
         'role' => 'nullable|in:fixed_income, irregular_income',
         'user_id' => 'nullable|exists:users,id',
         'job_sector' => 'required|string',
         'job_title' => 'nullable|string',
         'job_position' => 'nullable|string',
         'salary_amount' => 'required|numeric',
         'password' => 'required|string|min:8|confirmed',

        ];
    }
    public function messages(): array
    {
        return [

            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'password.required' => 'The password field is required.',
            'phone_number.required' => 'The phone number field is required.',
            'salary_type.required' => 'The salary type field is required.',
        ];

    }
}
