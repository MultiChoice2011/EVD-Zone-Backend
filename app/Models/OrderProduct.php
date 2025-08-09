<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class OrderProduct extends BaseModel
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'brand_id',
        'vendor_id',
        'type',
        'status',
        'total',
        'quantity',
        'coins_number',
        'unit_price',
        'nominal_price',
        'cost_price',
        'total_cost',
        'profit',
        'tax_value',
    ];

    use HasFactory;

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderProductSerials(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderProductSerial::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(OrderProductOption::class);
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function brand(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function vendor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function topupTransaction(): HasOne
    {
        return $this->hasOne(TopupTransaction::class);
    }

//    public function getTotalAttribute($value)
//    {
//        return $this->makeConversion($value);
//    }
//
//    public function getUnitPriceAttribute($value)
//    {
//        return $this->makeConversion($value);
//    }
//
//    public function getTaxValueAttribute($value)
//    {
//        return $this->makeConversion($value);
//    }
//
//    private function makeConversion($value)
//    {
//        // Get the authenticated seller
//        $authSeller = Auth::guard('sellerApi')->user();
//        if($authSeller){
//            // Fetch the seller's currency value
//            $sellerCurrencyValue = $authSeller->currency?->value;
//            // Fetch the wallet's currency value
//            $orderCurrencyValue = $this->order->currency?->value;
//            // Perform conversion: wallet_amount in seller's currency
//            return $value / ($orderCurrencyValue / $sellerCurrencyValue);
//        }else{
//            return $value;
//        }
//    }
}
