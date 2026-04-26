<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImageValidateRequest extends FormRequest
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
            'file' => 'required|file|mimes:jpg,jpeg,png|max:2048', // max 2MB
            'folder' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'Hình ảnh là bắt buộc',
            'file.file' => 'Hinh ảnh phải là một file',
            'file.mimes' => 'Hình ảnh phải là một tệp hình ảnh (jpg, jpeg, png)',
            'file.max' => 'Kích thước tệp phải nhỏ hơn 2MB',
        ];
    }
}
