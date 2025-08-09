<?php

namespace App\Models;


use App\Traits\TranslatedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Setting extends BaseModel
{

    use HasFactory, TranslatedAttributes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'is_translatable',
        'plain_value'
    ];

    public $translatedAttributes = ['value'];


    public function translations(): HasMany
    {
        return $this->hasMany(SettingTranslation::class);
    }





}
