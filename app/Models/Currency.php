<?php

namespace App\Models;

use App\Enums\GeneralStatusEnum;
use App\Traits\TranslatedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class Currency extends BaseModel
{
    use TranslatedAttributes;

    protected $fillable = [
        'value',
        'status',
        'decimal_place',
        'is_default',
        'country_id'
    ];

    public $translatedAttributes = [
        'name',
        'code',
    ];

    public function translations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CurrencyTranslation::class);
    }
    public function country() : BelongsTo
    {
        return $this->belongsTo(Country::class,'country_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::updating(function ($model) {
            $originalData = $model->getOriginal();
            Log::info('Model updated', [
                'original' => $originalData,
            ]);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', GeneralStatusEnum::getStatusActive());
    }
    public function scopeDefault($query)
    {
        return $query->where('is_default',1);
    }

}
