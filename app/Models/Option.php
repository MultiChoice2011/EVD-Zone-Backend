<?php

namespace App\Models;

use App\Traits\TranslatedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Option extends BaseModel
{
    use  TranslatedAttributes;

    public $translatedAttributes = [
        'name',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'sort_order', 'key'
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(OptionTranslation::class);
    }

    public function option_values(): HasMany
    {
        return $this->hasMany(OptionValue::class);
    }


}
