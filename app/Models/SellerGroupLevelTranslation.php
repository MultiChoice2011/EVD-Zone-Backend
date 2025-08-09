<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerGroupLevelTranslation extends Model
{
    protected $fillable = [
        'seller_group_level_id',
        'language_id',
        'name',
        'desc',
    ];
    public $timestamps = false;

    public function language(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
    public function sellerGroup(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SellerGroupLevel::class);
    }
}
