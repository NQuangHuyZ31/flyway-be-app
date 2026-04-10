<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockOutputVoucher extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'stock_outs';

    protected $fillable = [
        'name',
        'voucher_code',
        'output_type',
        'order_id',
        'warehouse_id',
        'section_id',
        'customer_id',
        'output_date',
        'invoice_number',
        'created_by',
        'approved_by',
        'approved_at',
        'completed_by',
        'completed_at',
        'status_id',
        'total_quantity',
        'total_cost',
        'notes',
        'rejection_reason',
    ];

    protected $casts = [
        'output_date' => 'date',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_quantity' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    /**
     * Warehouse relationship
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    /**
     * Section relationship
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(WarehouseSection::class, 'section_id', 'id');
    }

    /**
     * Created by user relationship
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Approved by user relationship
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    /**
     * Completed by user relationship
     */
    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by', 'id');
    }

    /**
     * Items relationship
     */
    public function items(): HasMany
    {
        return $this->hasMany(StockOutputVoucherItem::class, 'stock_out_id', 'id');
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_id', $status);
    }

    /**
     * Scope to filter by warehouse
     */
    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    /**
     * Scope to filter by output type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('output_type', $type);
    }

    /**
     * Scope to get pending vouchers
     */
    public function scopePending($query)
    {
        return $query->whereNull('approved_at');
    }
}
