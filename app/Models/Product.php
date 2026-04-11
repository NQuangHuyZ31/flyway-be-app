<?php

namespace App\Models;

use App\Traits\LangMapping;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['id','product_name', 'product_code', 'sku', 'category_id', 'unit_id', 'description', 'price', 'cost', 'minimum_inventory', 'total_quantity', 'product_image_url', 'is_active', 'created_at', 'updated_at'])]
class Product extends Model
{
    //
    use SoftDeletes, HasFactory, LangMapping;

    const FILTERS = [
        'product_name',
        'product_code',
        'sku',
        'category_id',
        'unit_id',
        'price',
        'cost',
        'minimum_inventory',
        'total_quantity',
        'is_active',
        'created_at',
    ];

    public static function getLangKey()
    {
        return self::mapLang(self::FILTERS);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitsOfMeasure::class, 'unit_id', 'id');
    }

}
