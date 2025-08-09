<?php

namespace App\Models;

use App\Enums\GeneralStatusEnum;
use App\Traits\TranslatedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Slider extends BaseModel
{

    use HasFactory, TranslatedAttributes;

    protected $fillable = [
        'order',
        'status',
        'brand_id',
        'display_in',
    ];

    public $translatedAttributes = [
        'title',
        'description',
        'image',
        'redirect_url',
        'alt_name'
    ];


    public function translations(): HasMany
    {
        return $this->hasMany(SliderTranslation::class);
    }



    public function scopeActive($query)
    {
        return $query->where('status', GeneralStatusEnum::getStatusActive());
    }


}
