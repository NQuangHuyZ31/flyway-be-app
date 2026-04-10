<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockOutputVoucherItemResource extends JsonResource
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
            'stock_out_id' => $this->stock_out_id,
            'product_id' => $this->product_id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'batch_id' => $this->batch_id,
            'line_number' => $this->line_number,
            'quantity_ordered' => $this->quantity_ordered,
            'quantity_shipped' => $this->quantity_shipped,
            'quantity_cancelled' => $this->quantity_cancelled,
            'unit_cost' => $this->unit_cost,
            'line_total' => $this->line_total,
            'notes' => $this->notes,
            'cancellation_notes' => $this->cancellation_notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
