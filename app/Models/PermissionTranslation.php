<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermissionTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'permission_id',
        'language_id',
        'display_name',
    ];

    public $timestamps = false;
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

}
