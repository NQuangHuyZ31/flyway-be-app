<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockInputVoucher extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'stock_ins';

    protected $fillable = [
        'name',
        'voucher_code',
        'input_type',
        'supplier_id',
        'warehouse_id',
        'section_id',
        'order_id',
        'input_date',
        'invoice_number',
        'created_by',
        'approved_by',
        'approved_at',
        'received_by',
        'received_at',
        'status_id',
        'total_quantity',
        'total_cost',
        'notes',
        'rejection_reason',
    ];

    protected $casts = [
        'input_date' => 'date',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
        'total_quantity' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    /**
     * Supplier relationship
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

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
     * Received by user relationship
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by', 'id');
    }

    /**
     * Items relationship
     */
    public function items(): HasMany
    {
        return $this->hasMany(StockInputVoucherItem::class, 'stock_in_id', 'id');
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
     * Scope to filter by input type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('input_type', $type);
    }

    /**
     * Scope to get pending vouchers
     */
    public function scopePending($query)
    {
        // You'll need to figure out the status_id for "pending"
        return $query->whereNotNull('created_at');
    }
}
