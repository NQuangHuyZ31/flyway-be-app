<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
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
            'product_id' => $this->product_id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'warehouse_id' => $this->warehouse_id,
            'warehouse' => new WarehouseResource($this->whenLoaded('warehouse')),
            'section_id' => $this->section_id,
            'section' => new WarehouseSectionResource($this->whenLoaded('section')),
            'quantity_on_hand' => $this->quantity_on_hand,
            'quantity_reserved' => $this->quantity_reserved,
            'quantity_available' => $this->quantity_available,
            'last_counted_at' => $this->last_counted_at,
            'last_counted_by' => $this->last_counted_by,
            'last_transaction_at' => $this->last_transaction_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
