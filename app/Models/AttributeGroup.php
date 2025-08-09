<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeGroup extends BaseModel
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','status',
    ];
    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }
    public function translations(): HasMany
    {
        return $this->hasMany(AttributeGroupTranslation::class);
    }


}
