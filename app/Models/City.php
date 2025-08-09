<?php

namespace App\Models;

use App\Traits\TranslatesName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends BaseModel
{
    use TranslatesName;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'region_id',
    ];

    protected $appends = ['name'];


    public function translations(): HasMany
    {
        return $this->hasMany(CityTranslation::class);
    }

    public function region(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

}
