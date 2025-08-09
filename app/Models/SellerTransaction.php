<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerTransaction extends BaseModel
{


    protected $fillable = [
        'seller_id',
        'amount',
        'note',
        'balance',
        'type',
        'currency_id'
    ];


    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
    public function currency():BelongsTo
    {
        return $this->belongsTo(Currency::class)->withDefault(function () {
            return Currency::where('is_default', 1)->first();
        });
    }

}
