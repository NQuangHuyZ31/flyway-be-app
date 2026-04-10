<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockInputVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // TODO: Add authorization logic
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'input_type' => 'sometimes|required|in:purchase_order,return_from_customer,transfer_in,adjustment,sample',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'warehouse_id' => 'sometimes|required|exists:warehouses,id',
            'section_id' => 'nullable|exists:warehouse_sections,id',
            'input_date' => 'sometimes|required|date',
            'invoice_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'items' => 'sometimes|array',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.quantity_ordered' => 'required_with:items|numeric|min:1',
            'items.*.unit_cost' => 'required_with:items|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'voucher_code.unique' => 'Voucher code already exists',
        ];
    }
}
