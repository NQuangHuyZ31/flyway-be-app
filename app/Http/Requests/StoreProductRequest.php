<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // TODO: Add authorization logic
    }

    public function rules(): array
    {
        return [
            'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|max:50|unique:products,product_code',
            'sku' => 'required|string|max:50|unique:products,sku|regex:/^[A-Z0-9\-]+$/',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'minimum_inventory' => 'required|numeric|min:1',
            'total_quantity' => 'required|numeric',
            'product_image_url' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'product_name.required' => 'Tên sản phâm là bắt buộc',
            'product_name.unique' => 'Tên sản phẩm đã tồn tại',
            'product_code.required' => 'Mã sản phẩm là bắt buộc',
            'product_code.unique' => 'Mã sản phẩm đã tồn tại',
            'sku.required' => 'SKU là bắt buộc',
            'sku.unique' => 'SKU đã được sử dụng',
            'sku.regex' => 'SKU phải chỉ chứa chữ hoa, số và dấu gạch ngang',
            'category_id.required' => 'Vui lòng chọn một danh mục',
            'category_id.exists' => 'Danh mục đã chọn không tồn tại',
            'unit_id.required' => 'Vui lòng chọn một đơn vị',
            'unit_id.exists' => 'Đơn vị đã chọn không tồn tại',
            'price.required' => 'Giá là bắt buộc',
            'price.numeric' => 'Giá phải là một số',
            'cost.required' => 'Chi phí là bắt buộc',
            'cost.numeric' => 'Chi phí phải là một số',
            'total_quantity.required' => 'Số lượng tổng là bắt buộc',
            'total_quantity.numeric' => 'Số lượng tổng phải là một số',
            'product_image_url.string' => 'URL hình ảnh sản phẩm phải là một chuỗi',
            'product_image_url.max' => 'URL hình ảnh sản phẩm không được vượt quá 255 ký tự',
        ];
    }
}
