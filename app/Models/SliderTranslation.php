<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SliderTranslation extends Model
{

    use HasFactory;

    protected $fillable = [
        'slider_id',
        'language_id',
        'title',
        'description',
        'image',
        'redirect_url',
        'alt_name',
    ];

    public $timestamps = false;

    public function getImageAttribute($value): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)){
            return $value;
        }
        elseif (isset($value)){
            return asset('storage/uploads/sliders' . '/' . $value);
        }
        else{
            return config('services.cloudinary.default_image');
        }
    }



    public function slider()
    {
        return $this->belongsTo(Slider::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

}
