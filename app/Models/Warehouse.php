<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'warehouse_name',
        'warehouse_code',
        'location',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Warehouse sections relationship
     */
    public function sections(): HasMany
    {
        return $this->hasMany(WarehouseSection::class, 'warehouse_id', 'id');
    }

    /**
     * Inventory relationship
     */
    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class, 'warehouse_id', 'id');
    }

    /**
     * Scope to get active warehouses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to search by name or code
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('warehouse_name', 'like', "%$term%")
            ->orWhere('warehouse_code', 'like', "%$term%");
    }
}
