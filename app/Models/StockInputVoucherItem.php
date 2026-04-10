<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockInputVoucherItem extends Model
{
    use HasFactory;

    protected $table = 'stock_in_items';

    public $timestamps = true;

    protected $fillable = [
        'stock_in_id',
        'product_id',
        'batch_id',
        'line_number',
        'quantity_ordered',
        'quantity_received',
        'quantity_rejected',
        'unit_cost',
        'line_total',
        'notes',
        'rejection_notes',
    ];

    protected $casts = [
        'quantity_ordered' => 'integer',
        'quantity_received' => 'integer',
        'quantity_rejected' => 'integer',
        'unit_cost' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    /**
     * Stock Input Voucher relationship
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(StockInputVoucher::class, 'stock_in_id', 'id');
    }

    /**
     * Product relationship
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Batch relationship
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class, 'batch_id', 'id');
    }
}
