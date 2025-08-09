<?php

namespace App\Models;

use App\Traits\TranslatedAttributes;
use App\Traits\TranslatesName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerGroupLevel extends BaseModel
{
    use SoftDeletes, TranslatedAttributes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'image',
        'status',
        'sort_order'
    ];


    public $translatedAttributes = [
        'name',
        'desc'
    ];


    public function translations(): HasMany
    {
        return $this->hasMany(SellerGroupLevelTranslation::class);
    }


    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function child(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }



    public function getImageAttribute($value): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)){
            return $value;
        }
        elseif (isset($value) && $value != 'no-image.png'){
            return asset('/storage/uploads/sellerGroups'). '/'.$value;
        }
        else{
            return config('services.cloudinary.default_image');
        }
    }

    public function scopeActive($query)
    {
        return $query->where('status', "active");
    }

}
