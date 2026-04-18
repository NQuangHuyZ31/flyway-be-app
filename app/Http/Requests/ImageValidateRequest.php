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
            'product_image_url' => 'required|file|mimes:jpg,jpeg,png|max:2048', // max 2MB
            'folder' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'product_image_url.required' => 'Hình ảnh là bắt buộc',
            'product_image_url.file' => 'Hinh ảnh phải là một file',
            'product_image_url.mimes' => 'Hình ảnh phải là một tệp hình ảnh (jpg, jpeg, png)',
            'product_image_url.max' => 'Kích thước tệp phải nhỏ hơn 2MB',
        ];
    }
}
