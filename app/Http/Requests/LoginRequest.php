<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'email' => 'required|email|regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/',
            'password' => 'required|string|min:6',
            'type' => 'required|in:email,google,facebook',
        ];
    }
    
    public function messages(): array {
            return [
                'email.required' => 'Email is required',
                'email.email' => 'Email must be a valid email address',
                'email.regex' => 'Email must be a valid Gmail address',
                'password.required' => 'Password is required',
                'password.string' => 'Password must be a string',
                'password.min' => 'Password must be at least 6 characters',
                'type.required' => 'Type is required',
                'type.in' => 'Type must be either email, google, or facebook',
            ];
        }
}
