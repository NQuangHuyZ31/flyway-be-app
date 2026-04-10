<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseSection extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'warehouse_id',
        'section_name',
        'section_code',
        'section_type',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Warehouse relationship
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    /**
     * Inventory relationship
     */
    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class, 'section_id', 'id');
    }

    /**
     * Scope to get active sections
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by warehouse
     */
    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    /**
     * Scope to filter by section type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('section_type', $type);
    }
}
