<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DirectPurchasePriority extends Model
{

    protected $fillable = [
        'direct_purchase_id', 'vendor_id', 'priority_level'
    ];

    public $timestamps = false;

    public function directPurchase(): BelongsTo
    {
        return $this->belongsTo(DirectPurchase::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }



}
