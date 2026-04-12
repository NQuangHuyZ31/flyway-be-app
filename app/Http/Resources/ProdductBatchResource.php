<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdductBatchResource extends JsonResource
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
            'batch_code' => $this->batch_code,
            'supplier' => $this->supplier->name,
            'import_date' => $this->import_date,
            'quantity_imported' => $this->quantity_imported,
            'type' => $this->type,
            'quantity_available' => $this->quantity_available,
        ];
    }
}
