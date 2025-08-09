<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeTranslation extends Model
{
    protected $fillable = [
        'attribute_id',
        'language_id',
        'name',
    ];
    public $timestamps = false;

    public function attribute(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
}
