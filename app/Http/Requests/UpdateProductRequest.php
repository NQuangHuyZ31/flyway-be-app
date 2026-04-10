<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // TODO: Add authorization logic
    }

    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            'product_name' => 'sometimes|required|string|max:255|unique:products,product_name,' . $productId,
            'product_code' => 'sometimes|required|string|max:50|unique:products,product_code,' . $productId,
            'sku' => 'sometimes|required|string|max:50|unique:products,sku,' . $productId . '|regex:/^[A-Z0-9\-]+$/',
            'category_id' => 'sometimes|required|exists:product_categories,id',
            'unit_id' => 'sometimes|required|exists:units,id',
            'description' => 'nullable|string|max:1000',
            'price' => 'sometimes|required|numeric|min:0',
            'cost' => 'sometimes|required|numeric|min:0',
            'minimum_inventory' => 'sometimes|required|numeric|min:0',
            'product_image_url' => 'nullable|url',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'product_name.unique' => 'Product name already exists',
            'product_code.unique' => 'Product code already exists',
            'sku.unique' => 'SKU already in use',
            'sku.regex' => 'SKU must be uppercase with numbers and hyphens only',
            'category_id.exists' => 'Selected category does not exist',
            'unit_id.exists' => 'Selected unit does not exist',
        ];
    }
}
