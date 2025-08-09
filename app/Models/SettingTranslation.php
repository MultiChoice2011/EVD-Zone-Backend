<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettingTranslation extends Model
{

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'setting_id',
        'language_id',
        'value'
    ];

    public $timestamps = false;

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

}
