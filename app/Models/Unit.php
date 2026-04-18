<?php

namespace App\Models;

use App\Traits\LangMapping;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    //
    use HasFactory, SoftDeletes, LangMapping;
    protected $table = 'units';
    
    protected $fillable = [
        'name',
        'code',
        'abbreviation',
        'is_active',
        'created_at',
    ];

    const FILTERS = [
        'name' => [
            'type' => 'string',
            'column' => 'name',
        ],
        'code' => [
            'type' => 'string',
            'column' => 'code',
        ],
        'is_active' => [
            'type' => 'boolean',
            'column' => 'is_active',
        ],
        'created_at' => [
            'type' => 'date',
            'column' => 'created_at',
        ],  
    ];

    public static function getLangKey()
    {
        return self::mapLang(self::FILTERS, 'unit');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'unit_id');
    }

    
}
