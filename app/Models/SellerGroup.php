<?php

namespace App\Models;

use App\Enums\GeneralStatusEnum;
use App\Traits\TranslatedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerGroup extends BaseModel
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
        'automatic',
        'amount_sales',
        'order_count',
        'auto_assign',
        'status',
        'sort_order',
        'conditions_type'
    ];

    public $translatedAttributes = [
        'name',
        'description',
    ];


    public function translations(): HasMany
    {
        return $this->hasMany(SellerGroupTranslation::class);
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(SellerGroupCondition::class);
    }

    public function seller_group_custom_product_prices(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SellerGroupCustomProductPrice::class);
    }

    public function seller_group_custom_prices(): HasMany
    {
        return $this->hasMany(SellerGroupCustomPrice::class);
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
        return $query->where('status', GeneralStatusEnum::getStatusActive());
    }

}
