<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockInputVoucherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'voucher_code' => $this->voucher_code,
            'input_type' => $this->input_type,
            'supplier_id' => $this->supplier_id,
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'warehouse_id' => $this->warehouse_id,
            'warehouse' => new WarehouseResource($this->whenLoaded('warehouse')),
            'section_id' => $this->section_id,
            'section' => new WarehouseSectionResource($this->whenLoaded('section')),
            'input_date' => $this->input_date,
            'invoice_number' => $this->invoice_number,
            'created_by' => $this->created_by,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'approved_by' => $this->approved_by,
            'approver' => new UserResource($this->whenLoaded('approver')),
            'approved_at' => $this->approved_at,
            'received_by' => $this->received_by,
            'receiver' => new UserResource($this->whenLoaded('receiver')),
            'received_at' => $this->received_at,
            'status_id' => $this->status_id,
            'total_quantity' => $this->total_quantity,
            'total_cost' => $this->total_cost,
            'notes' => $this->notes,
            'rejection_reason' => $this->rejection_reason,
            'items' => StockInputVoucherItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
