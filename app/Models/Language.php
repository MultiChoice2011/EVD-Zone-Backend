<?php

namespace App\Models;

use App\Enums\GeneralStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends BaseModel
{

    protected $fillable = ['name', 'code', 'locale', 'image', 'directory', 'status', 'sort_order'];

    public function getImageAttribute($value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL)){
            return $value;
        }
        elseif (isset($value) && $value != 'no-image.png'){
            return asset('/storage/uploads/languages'). '/'.$value;
        }
        else{
            return config('services.cloudinary.default_image');
        }
    }
    public function categoryTranslations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', GeneralStatusEnum::getStatusActive());
    }
}
