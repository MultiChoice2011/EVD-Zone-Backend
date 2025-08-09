<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'language_id',
        'display_name',
    ];

    public $timestamps = false;
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

}
