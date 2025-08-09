<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegionTranslation extends Model
{
    protected $fillable = [
        'region_id',
        'language_id',
        'name',
    ];
    public $timestamps = false;

    public function region(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
