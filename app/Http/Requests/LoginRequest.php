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
                'email.required' => 'Email không được để trống',
                'email.email' => 'Email phải là một địa chỉ email hợp lệ',
                'email.regex' => 'Email phải là một địa chỉ Gmail hợp lệ',
                'password.required' => 'Mật khẩu không được để trống',
                'password.string' => 'Mật khẩu phải là một chuỗi',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                'type.required' => 'Loại không được để trống',
                'type.in' => 'Loại phải là email, google, hoặc facebook',
            ];
        }
}
