<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockOutputVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // TODO: Add authorization logic
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'voucher_code' => 'required|string|max:100|unique:stock_outs,voucher_code',
            'output_type' => 'required|in:sale,return_to_supplier,transfer_out,adjustment,sample',
            'warehouse_id' => 'required|exists:warehouses,id',
            'section_id' => 'nullable|exists:warehouse_sections,id',
            'customer_id' => 'nullable|exists:customers,id',
            'output_date' => 'required|date',
            'invoice_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_ordered' => 'required|numeric|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Voucher name is required',
            'voucher_code.required' => 'Voucher code is required',
            'voucher_code.unique' => 'Voucher code already exists',
            'output_type.required' => 'Output type is required',
            'warehouse_id.required' => 'Warehouse is required',
            'output_date.required' => 'Output date is required',
            'items.required' => 'At least one item is required',
            'items.*.product_id.required' => 'Product is required for each item',
            'items.*.quantity_ordered.required' => 'Quantity is required for each item',
        ];
    }
}
