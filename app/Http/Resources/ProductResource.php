<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'product_name' => $this->product_name,
            'product_code' => $this->product_code,
            'sku' => $this->sku,
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ],
            'unit' => [
                'id' => $this->unit?->id,
                'name' => $this->unit?->name,
            ],
            'price' => $this->price,
            'cost' => $this->cost,
            'minimum_inventory' => $this->minimum_inventory,
            'total_quantity' => $this->total_quantity,
            'description' => $this->description,
            'product_image_url' => config('filesystems.disks.s3.url') .'/'. $this->product_image_url,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
