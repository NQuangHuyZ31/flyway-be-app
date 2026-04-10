<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // TODO: Add authorization logic
    }

    public function rules(): array
    {
        return [
            'product_id' => 'sometimes|required|exists:products,id',
            'warehouse_id' => 'sometimes|required|exists:warehouses,id',
            'section_id' => 'nullable|exists:warehouse_sections,id',
            'quantity_on_hand' => 'sometimes|required|numeric|min:0',
            'quantity_reserved' => 'sometimes|required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.exists' => 'Product does not exist',
            'warehouse_id.exists' => 'Warehouse does not exist',
        ];
    }
}
