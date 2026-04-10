<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOutputVoucherItem extends Model
{
    use HasFactory;

    protected $table = 'stock_out_items';

    public $timestamps = true;

    protected $fillable = [
        'stock_out_id',
        'product_id',
        'batch_id',
        'line_number',
        'quantity_ordered',
        'quantity_shipped',
        'quantity_cancelled',
        'unit_cost',
        'line_total',
        'notes',
        'cancellation_notes',
    ];

    protected $casts = [
        'quantity_ordered' => 'integer',
        'quantity_shipped' => 'integer',
        'quantity_cancelled' => 'integer',
        'unit_cost' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    /**
     * Stock Output Voucher relationship
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(StockOutputVoucher::class, 'stock_out_id', 'id');
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
