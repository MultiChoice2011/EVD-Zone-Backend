<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerTranslations extends Model
{
    protected $table = 'seller_translations';
    protected $fillable = [
        'seller_id',
        'language_id',
        'reject_reason',
    ];

    public $timestamps = false;
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }





}
