<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeSectionTranslation extends Model
{
    protected $fillable = [
        'home_section_id',
        'language_id',
        'name',
        'title',
        'display',
        'image',
        'redirect_url',
        'alt_name'
    ];
    public $timestamps = false;

    public function homeSection(): BelongsTo
    {
        return $this->belongsTo(HomeSection::class);
    }


    public function getImageAttribute($value)
    {
        if (!$value || filter_var($value, FILTER_VALIDATE_URL)){
            return $value;
        }
        else{
            return asset('/storage/uploads/homeSections') . '/' . $value;
            // return config('services.cloudinary.default_image');
        }
    }

}
