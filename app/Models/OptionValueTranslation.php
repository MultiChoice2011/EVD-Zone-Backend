<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OptionValueTranslation extends Model
{
    protected $fillable = [
        'option_value_id',
        'language_id',
        'name',
    ];
    public $timestamps = false;

    public function option_value(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OptionValue::class);
    }
}
