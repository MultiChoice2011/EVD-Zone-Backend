<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankCommissionTranslation extends Model
{
    use HasFactory;
    protected $fillable = [
        'language_id',
        'name',
        'bank_commission_id'
    ];
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class,'language_id');
    }
    public function bankCommission(): BelongsTo
    {
        return $this->belongsTo(BankCommission::class,'bank_commission_id');
    }
}
