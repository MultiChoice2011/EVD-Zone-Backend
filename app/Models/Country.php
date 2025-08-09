<?php

namespace App\Models;

use App\Traits\TranslatesName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Country extends BaseModel
{
    use TranslatesName;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'flag',
        'vat',
        'code',
    ];

    protected $appends = ['name'];


    public function translations(): HasMany
    {
        return $this->hasMany(CountryTranslation::class);
    }

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    public function cities()
    {
        return $this->hasManyThrough(City::class, Region::class);
    }
    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }
    public function banks()
    {
        return $this->belongsToMany(Bank::class, 'bank_country', 'country_id', 'bank_id');
    }
    public function getFlagAttribute($value): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)){
            return $value;
        }
        elseif (isset($value)) {
            return asset('storage/uploads/countries' . '/' . $value);
        }
        else{
            return config('services.cloudinary.default_image');
        }
    }

    public function currency(): HasOne
    {
        return $this->hasOne(Currency::class);
    }

}
