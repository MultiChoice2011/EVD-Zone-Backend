<?php

namespace App\Models;

use App\Enums\GeneralStatusEnum;
use App\Traits\TranslatedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomeSection extends BaseModel
{
    use HasFactory, TranslatedAttributes;

    protected $fillable = [
        'order',
        'type',
        'display_in',
        'status',
        'style',
        'banner_category_id',
        'brand_id',
    ];

    public $translatedAttributes = [
        'name',
        'title',
        'display',
        'image',
        'redirect_url',
        'alt_name'
    ];


    public function translations(): HasMany
    {
        return $this->hasMany(HomeSectionTranslation::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function bannerCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'home_section_categories');
    }

    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'home_section_brands');
    }




    public function scopeActive($query)
    {
        return $query->where('status', GeneralStatusEnum::getStatusActive());
    }



}
