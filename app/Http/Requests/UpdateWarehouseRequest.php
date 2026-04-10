<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // TODO: Add authorization logic
    }

    public function rules(): array
    {
        $warehouseId = $this->route('warehouse');

        return [
            'warehouse_name' => 'sometimes|required|string|max:255|unique:warehouses,warehouse_name,' . $warehouseId,
            'warehouse_code' => 'sometimes|required|string|max:50|unique:warehouses,warehouse_code,' . $warehouseId,
            'location' => 'sometimes|required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'warehouse_name.unique' => 'Warehouse name already exists',
            'warehouse_code.unique' => 'Warehouse code already exists',
        ];
    }
}
