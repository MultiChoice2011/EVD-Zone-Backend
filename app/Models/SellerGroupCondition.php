<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerGroupCondition extends Model
{
    protected $fillable = [
        'seller_group_id', 'condition_type', 'comparison_operator', 'value', 'value2'
    ];

    public $timestamps = false;
    public function seller_group(): BelongsTo
    {
        return $this->belongsTo(SellerGroup::class);
    }


}
