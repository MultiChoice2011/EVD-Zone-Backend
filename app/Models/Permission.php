<?php

namespace App\Models;

use App\Traits\TranslatedAttributes;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permission extends SpatiePermission
{
    use TranslatedAttributes;

    public $translatedAttributes = ['display_name'];
    public function translations(): HasMany
    {
        return $this->hasMany(PermissionTranslation::class);
    }

}
