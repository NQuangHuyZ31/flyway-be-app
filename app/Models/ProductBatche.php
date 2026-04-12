<?php

namespace App\Models;

use App\Traits\LangMapping;
use Illuminate\Database\Eloquent\Model;

class ProductBatche extends Model
{
    //
    use LangMapping;

    protected $fillable = ['id', 'product_id', 'batch_code', 'supplier_id', 'import_date', 
    'quantity_imported', 'type', 'quantity_available', 'unit_cost', 'unit_price', 'expiry_date',
    'status', 'total_cost', 'created_at', 'updated_at'];

    const FILTERS = [
        'batch_code' => [
            'type' => 'string',
            'column' => 'batch_code',
        ],
        'supplier_id' => [
            'type' => 'integer',
            'column' => 'supplier_id',
        ],
        'import_date' => [
            'type' => 'date',
            'column' => 'import_date',
        ],
        'quantity_imported' => [
            'type' => 'integer',
            'column' => 'quantity_imported',
        ],
        'type' => [
            'type' => 'string',
            'column' => 'type',
        ],
        'quantity_available' => [
            'type' => 'integer',
            'column' => 'quantity_available',
        ],
        'status' => [
            'type' => 'string',
            'column' => 'status',
        ],
        'created_at' => [
            'type' => 'date',
            'column' => 'created_at',
        ],
    ];

    public static function getLangKey()
    {
        return self::mapLang(self::FILTERS, 'product_batche');
    }
}
