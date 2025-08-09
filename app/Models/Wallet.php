<?php

namespace App\Models;

use App\Services\General\CurrencyService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Wallet extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'recharge_balance_type',
        'bank_name',
        'transferring_name',
        'receipt_image',
        'notes',
        'amount',
        'type',
        'seller_id',
        'subseller_id',
        'currency_id',
        'bank_id',
        'status',
        'transferring_account_number'
    ];
    public function seller() : BelongsTo
    {
        return $this->belongsTo(Seller::class,'seller_id');
    }
    public function bank() : BelongsTo
    {
        return $this->belongsTo(Bank::class,'bank_id');
    }
    public function currency() : BelongsTo
    {
        return $this->belongsTo(Currency::class,'currency_id');
    }
    public function getReceiptImageUrlAttribute($value): string
    {
        if (filter_var($this->receipt_image, FILTER_VALIDATE_URL)){
            return $this->receipt_image;
        }
        elseif (isset($this->receipt_image) && $this->receipt_image != 'no-image.png'){
            return asset('/storage/uploads/receipts'). '/'.$value;
        }
        else{
            return config('services.cloudinary.default_image');
        }
    }
    public function scopePending($query)
    {
        return $query->whereStatus('pending');
    }
    public function scopeComplete($query)
    {
        return $query->whereStatus('complete');
    }
    // public function getAmountAttribute($value)
    // {
    //     // Get the authenticated seller
    //     $authSeller = Auth::guard('sellerApi')->user();
    //     if($authSeller){
    //         // Fetch the seller's currency value
    //         $sellerCurrencyValue = $authSeller->currency?->value;
    //         // Fetch the wallet's currency value
    //         $walletCurrencyValue = $this->currency?->value;
    //         // Perform conversion: wallet_amount in seller's currency
    //         return $value / ($walletCurrencyValue / $sellerCurrencyValue);
    //     }else{
    //         return $value;
    //     }
    // }
}
