<?php

namespace App\Models;

use App\Traits\TranslatedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OptionValue extends BaseModel
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
        'option_id', 'image', 'sort_order', 'key'
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(OptionValueTranslation::class);
    }
    public function option(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Option::class);
    }


}
