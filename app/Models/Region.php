<?php

namespace App\Models;

use App\Traits\TranslatesName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends BaseModel
{

    use TranslatesName;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_id',
    ];

    protected $appends = ['name'];


    public function translations(): HasMany
    {
        return $this->hasMany(RegionTranslation::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class);
    }


}
