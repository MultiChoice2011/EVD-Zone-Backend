<?php

namespace App\Models;


use App\Traits\TranslatedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaticPage extends BaseModel
{

    use SoftDeletes, HasFactory, TranslatedAttributes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'status',
        'web',
        'mobile',
    ];

    public $translatedAttributes = ['title','meta_title','meta_description','meta_keywords','content'];


    public function translations(): HasMany
    {
        return $this->hasMany(StaticPageTranslation::class);
    }





}
