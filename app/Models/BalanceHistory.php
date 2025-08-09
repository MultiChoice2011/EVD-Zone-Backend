<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BalanceHistory extends BaseModel
{

    use HasFactory;

    protected $fillable = ['order_id', 'ownerble_type', 'ownerble_id','balance_before', 'balance_after'];

    public function ownerble()
    {
        return $this->morphTo();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }





}
