<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
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
            'product_code' => $this->product_code,
            'sku' => $this->sku,
            'product_name' => $this->product_name,
            'category_id' => $this->category->name,
            'unit_id' => $this->unit->name,
            'price' => $this->price,
            'cost' => $this->cost,
            'minimum_inventory' => $this->minimum_inventory,
            'total_quantity' => $this->total_quantity,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'product_image_url' => $this->product_image_url,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
