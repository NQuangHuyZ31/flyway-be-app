<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitsOfMeasure extends Model
{
    //
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'id',
        'name',
        'code',
        'abbreviation',
        'is_active'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'unit_id', 'id');
    }
}
