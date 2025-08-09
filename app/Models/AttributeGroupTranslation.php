<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeGroupTranslation extends Model
{
    protected $fillable = [
        'attribute_group_id',
        'language_id',
        'name',
    ];
    public $timestamps = false;

    public function attribute(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AttributeGroup::class);
    }
}
