<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyTranslation extends Model
{
    protected $fillable = [
        'currency_id',
        'language_id',
        'name',
        'code',
    ];
    public $timestamps = false;

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
