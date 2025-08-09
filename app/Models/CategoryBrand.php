<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryBrand extends Model
{
    protected $fillable = [
        'brand_id',
        'category_id',
    ];
    public $timestamps = false;

    public function brand(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
