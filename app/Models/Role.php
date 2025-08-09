<?php

namespace App\Models;

use App\Enums\GeneralStatusEnum;
use App\Traits\TranslatedAttributes;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends SpatieRole
{
    use TranslatedAttributes;

    protected $fillable = [
        'name',
        'guard_name',
        'seller_id',
    ];

    public $translatedAttributes = ['display_name'];


    public function translations(): HasMany
    {
        return $this->hasMany(RoleTranslation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', GeneralStatusEnum::getStatusActive());
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function scopeForSeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }
}
