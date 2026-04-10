<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // TODO: Add authorization logic
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'section_id' => 'nullable|exists:warehouse_sections,id',
            'quantity_on_hand' => 'required|numeric|min:0',
            'quantity_reserved' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Product is required',
            'product_id.exists' => 'Product does not exist',
            'warehouse_id.required' => 'Warehouse is required',
            'warehouse_id.exists' => 'Warehouse does not exist',
            'quantity_on_hand.required' => 'Quantity on hand is required',
            'quantity_reserved.required' => 'Quantity reserved is required',
        ];
    }
}
