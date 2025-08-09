<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaticPageTranslation extends Model
{

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'static_page_id',
        'language_id',
        'title',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'content'
    ];

    public $timestamps = false;

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

}
