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
            'product_name' => 'required|string|max:255|unique:products,product_name',
            'product_code' => 'required|string|max:50|unique:products,product_code',
            'sku' => 'required|string|max:50|unique:products,sku|regex:/^[A-Z0-9\-]+$/',
            'category_id' => 'required|exists:product_categories,id',
            'unit_id' => 'required|exists:units,id',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'minimum_inventory' => 'required|numeric|min:0',
            'product_image_url' => 'nullable|url',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'product_name.required' => 'Product name is required',
            'product_name.unique' => 'Product name already exists',
            'product_code.required' => 'Product code is required',
            'product_code.unique' => 'Product code already exists',
            'sku.required' => 'SKU is required',
            'sku.unique' => 'SKU already in use',
            'sku.regex' => 'SKU must be uppercase with numbers and hyphens only',
            'category_id.required' => 'Please select a category',
            'category_id.exists' => 'Selected category does not exist',
            'unit_id.required' => 'Please select a unit',
            'unit_id.exists' => 'Selected unit does not exist',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
            'cost.required' => 'Cost is required',
            'cost.numeric' => 'Cost must be a number',
        ];
    }
}
