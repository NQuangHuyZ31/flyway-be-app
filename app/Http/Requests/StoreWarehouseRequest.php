<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // TODO: Add authorization logic
    }

    public function rules(): array
    {
        return [
            'warehouse_name' => 'required|string|max:255|unique:warehouses,warehouse_name',
            'warehouse_code' => 'required|string|max:50|unique:warehouses,warehouse_code',
            'location' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'warehouse_name.required' => 'Warehouse name is required',
            'warehouse_name.unique' => 'Warehouse name already exists',
            'warehouse_code.required' => 'Warehouse code is required',
            'warehouse_code.unique' => 'Warehouse code already exists',
            'location.required' => 'Location is required',
        ];
    }
}
