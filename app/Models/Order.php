<?php

namespace App\Models;

use App\Services\General\CurrencyService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Order extends BaseModel
{
    use SoftDeletes, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'status',
        'owner_type',
        'owner_id',
        'subseller_id',
        'currency_id',
        'payment_method',
        'total',
        'sub_total',
        'total_cost',
        'profit',
        'vat',
        'tax',
        'order_source',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function balanceHistory()
    {
        return $this->hasOne(BalanceHistory::class);
    }

    public function userPulled(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, OrderUser::class, 'order_id', 'id', 'id', 'user_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function order_products(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function orderGift(): HasOne
    {
        return $this->hasOne(OrderGift::class);
    }

    public function failedReasons()
    {
        return $this->hasMany(FailedOrderReason::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(OrderPaymentTransaction::class);
    }

    public function topupTransaction(): HasMany
    {
        return $this->hasMany(TopupTransaction::class);
    }

    public function orderPaymentReceipt(): HasOne
    {
        return $this->hasOne(OrderPaymentReceipt::class);
    }

    public function order_histories(): HasMany
    {
        return $this->hasMany(OrderHistory::class);
    }
    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class,'order_id');
    }
    // public function getTotalAttribute($value)
    // {
    //     return $this->makeConversion($value);
    // }

    // public function getSubTotalAttribute($value)
    // {
    //     return $this->makeConversion($value);
    // }

    // public function getVatAttribute($value)
    // {
    //     return $this->makeConversion($value);
    // }

    // public function getTaxAttribute($value)
    // {
    //     return $this->makeConversion($value);
    // }

    // private function makeConversion($value)
    // {
    //     // Get the authenticated seller
    //     $authSeller = Auth::guard('sellerApi')->user();
    //     if($authSeller){
    //         // Fetch the seller's currency value
    //         $sellerCurrencyValue = $authSeller->currency?->value;

    //         // Fetch the wallet's currency value
    //         $orderCurrencyValue = $this->currency?->value;
    //         // Perform conversion: wallet_amount in seller's currency
    //         return $value / ($orderCurrencyValue / $sellerCurrencyValue);
    //     }else{
    //         return $value;
    //     }
    // }
}
