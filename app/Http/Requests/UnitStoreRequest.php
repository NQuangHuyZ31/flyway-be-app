<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UnitStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255|min:2',
            'code' => 'required|string|max:50',
            'abbreviation' => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên đơn vị là bắt buộc.',
            'name.string' => 'Tên đơn vị phải là một chuỗi.',
            'name.max' => 'Tên đơn vị không được vượt quá 255 ký tự.',
            'name.min' => 'Tên đơn vị phải có ít nhất 2 ký tự.',
            'code.required' => 'Mã đơn vị là bắt buộc.',
            'code.string' => 'Mã đơn vị phải là một chuỗi.',
            'code.max' => 'Mã đơn vị không được vượt quá 50 ký tự.',
            'code.unique' => 'Mã đơn vị đã tồn tại.',
            'abbreviation.string' => 'Ký hiệu phải là một chuỗi.',
            'abbreviation.max' => 'Ký hiệu không được vượt quá 20 ký tự.',
        ];
    }
}
