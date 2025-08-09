<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerRejectReason extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'reason',
        'resolved_at'
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function scopeUnresolved($query)
    {
        return $query->whereNull('resolved_at');
    }
}
