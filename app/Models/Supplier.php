<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'supplier_name',
        'supplier_code',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'tax_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Stock input vouchers relationship
     */
    public function stockInputVouchers(): HasMany
    {
        return $this->hasMany(StockInputVoucher::class, 'supplier_id', 'id');
    }

    /**
     * Scope to get active suppliers
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
        return $query->where('supplier_name', 'like', "%$term%")
            ->orWhere('supplier_code', 'like', "%$term%")
            ->orWhere('email', 'like', "%$term%");
    }
}
