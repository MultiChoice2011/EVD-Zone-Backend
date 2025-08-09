<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandTranslation extends Model
{
    protected $fillable = [
        'brand_id',
        'language_id',
        'name',
        'description',
    ];
    public $timestamps = false;

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
