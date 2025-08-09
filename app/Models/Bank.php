<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TranslatedAttributes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends BaseModel
{
    use HasFactory,TranslatedAttributes,SoftDeletes;
    protected $fillable = ['account_number','iban_number'];
    public $translatedAttributes = [
        'name',
        'description',
    ];
    public function translations() : HasMany
    {
        return $this->hasMany(BankTranslation::class);
    }
    public function countries()
    {
        return $this->belongsToMany(Country::class, 'bank_country', 'bank_id', 'country_id');
    }
}
