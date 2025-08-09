<?php

namespace App\Models;

use App\Enums\GeneralStatusEnum;
use App\Traits\TranslatedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends BaseModel
{
    use TranslatedAttributes;

    protected $fillable = ['status'];

    public $translatedAttributes = [
        'name',
        'description',
    ];

    protected $hidden = ['pivot'];



    public function images(): HasMany
    {
        return $this->hasMany(BrandImage::class);
    }

    public function translations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BrandTranslation::class);
    }

    public function category_brands(): HasMany
    {
        return $this->hasMany(CategoryBrand::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_brands');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }


    public function getImagesArray(): array
    {
        $images = [];
        foreach ($this->images as $image) {
            $images[$image->key] = $image->image;
        }
        unset($this->images);
        return $images;
    }


    public function scopeActive($query)
    {
        return $query->where('status', GeneralStatusEnum::getStatusActive());
    }
}
