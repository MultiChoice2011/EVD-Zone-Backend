<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankCommissionSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'bank_commission_id',
        'name',
        'gate_fees',
        'static_value',
        'additional_value_fees'
    ];
    public function bankCommission():BelongsTo
    {
        return $this->belongsTo(BankCommission::class,'bank_commission_id');
    }
}
