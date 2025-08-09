<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryTranslation extends Model
{
    protected $fillable = [
        'country_id',
        'language_id',
        'name',
    ];
    public $timestamps = false;

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
