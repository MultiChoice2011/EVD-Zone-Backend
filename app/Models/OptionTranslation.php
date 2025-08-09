<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OptionTranslation extends Model
{
    protected $fillable = [
        'option_id',
        'language_id',
        'name',
    ];
    public $timestamps = false;

    public function option(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Option::class);
    }
}
