<?php

namespace App\Models;

use App\Traits\TranslatedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BrandImage extends Model
{

    protected $fillable = ['brand_id', 'key', 'image'];

    public $timestamps = false;

    public function getImageAttribute($value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL)){
            return $value;
        }
        elseif (isset($value)) {
            return asset('/storage/uploads/brands') . '/' . $value;
        }
        else{
            return config('services.cloudinary.default_image');
        }
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}
