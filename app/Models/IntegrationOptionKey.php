<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IntegrationOptionKey extends Model
{
    protected $fillable = [
        'integration_id',
        'parent_id',
        'key',
        'type',
        'value'
    ];

    public $timestamps = false;

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function childrenKeys(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

}
