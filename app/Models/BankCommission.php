<?php

namespace App\Models;

use App\Enums\GeneralStatusEnum;
use App\Traits\TranslatedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankCommission extends Model
{
    use HasFactory,TranslatedAttributes;
    protected $fillable = ['status'];
    public $translatedAttributes = ['name'];
    public function scopeActive($query)
    {
        return $query->where('status', GeneralStatusEnum::getStatusActive());
    }
    public function translations(): HasMany
    {
        return $this->hasMany(BankCommissionTranslation::class,'bank_commission_id');
    }
    public function settings() : HasMany
    {
        return $this->hasMany(BankCommissionSetting::class,'bank_commission_id');
    }
}
