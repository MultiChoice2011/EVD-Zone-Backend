<?php

namespace App\Models;

use App\Enums\GeneralStatusEnum;
use App\Enums\ProductTaxType;
use App\Services\General\CurrencyService;
use App\Traits\TranslatedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Product extends BaseModel
{
    use TranslatedAttributes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'brand_id', 'type', 'vendor_id', 'serial', 'quantity', 'image', 'coins_number', 'price', 'cost_price', 'points', 'status', 'sort_order', 'web', 'mobile',
        'sku', 'notify', 'minimum_quantity', 'max_quantity', 'wholesale_price', 'tax_id', 'packing_method', 'tax_type', 'tax_amount', 'is_live_integration', 'is_available'
    ];

    protected $appends = [
        'profit_rate',
        'calc_tax_amount',
        'calc_tax_type',
        'calc_tax_rate',
        'is_favorite',
    ];

    public $translatedAttributes = [
        'name',
        'desc',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'long_desc',
        'content'
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }
    public function product_images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories')->withPivot('category_id', 'product_id');
    }
    public function productCategory(): HasOne
    {
        return $this->hasOne(ProductCategory::class);
    }

    public function product_options(): HasMany
    {
        return $this->hasMany(ProductOption::class)->with(['option','option.option_values']);
    }
    public function options()
    {
        return $this->hasManyThrough(Option::class, ProductOption::class);
    }

    public function order_products(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }


    public function productDiscountSellerGroup(): HasMany
    {
        return $this->hasMany(ProductDiscountSellerGroup::class);
    }

    public function vendorProducts(): HasMany
    {
        return $this->hasMany(VendorProduct::class);
    }



    public function productPriceSellerGroup(): HasMany
    {
        return $this->hasMany(ProductPriceSellerGroup::class)->with('seller_group');
    }

    public function productSerials(): HasMany
    {
        return $this->hasMany(ProductSerial::class);
    }

    public function valueAddedTax(): BelongsTo
    {
        return $this->belongsTo(ValueAddedTax::class, 'tax_id');
    }
    // Define the relationship to favorites
    public function favorites() :HasMany
    {
        return $this->hasMany(Favorite::class,'product_id');
    }
    // Define the relationship with category including translations
//    public function category(): BelongsTo
//    {
//        return $this->belongsTo(Category::class)->with('translations');
//    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }


    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function attributes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'product_attribute');
    }

    // public function ratings()
    // {
    //     return $this->hasMany(Rating::class);
    // }

    public function directPurchase(): HasOne
    {
        return $this->hasOne(DirectPurchase::class);
    }

    // Override the price attribute accessor
    public function getPriceAttribute($value)
    {
        // Get the authenticated seller
        $authSeller = Auth::guard('sellerApi')->user();
        // Default to the original price
        $finalPrice = $value;
        // Now apply the currency conversion
        $currency = CurrencyService::getCurrentCurrency($authSeller);
        // Return the final price multiplied by the currency conversion rate
        return $finalPrice * $currency->value;
    }
    // Override the price attribute accessor
    public function getWholesalePriceAttribute($value)
    {
        // Get the authenticated seller
        $authSeller = Auth::guard('sellerApi')->user();
        // Default to the original price
        $finalPrice = $value;
        // Check if a seller is authenticated and belongs to a seller group
        if ($authSeller && $authSeller->sellerGroup) {
            // Get the seller group ID
            $sellerGroupId = $authSeller->sellerGroup?->id ?? null;
            if ($sellerGroupId) {
                // Attempt to find a custom price for this product and seller group
                $customPrice = ProductPriceSellerGroup::where('product_id', $this->id)
                    ->where('seller_group_id', $sellerGroupId)
                    ->first();
                // If a custom price exists, use it
                if ($customPrice) {
                    $finalPrice = $customPrice->price;
                }
            }
        }
        // Now apply the currency conversion
        $currency = CurrencyService::getCurrentCurrency($authSeller);
        // Return the final price multiplied by the currency conversion rate
        return $finalPrice * $currency->value;
    }

    // Override the price attribute accessor
    public function getCostPriceAttribute($value)
    {
        // Get the authenticated admin
        $authAdmin = Auth::guard('adminApi')->user();
        if ($authAdmin){
            return $value;
        }

        // Get the authenticated seller
        $authSeller = Auth::guard('sellerApi')->user();
        if (!$authSeller){
            return $value;
        }

        // Now apply the currency conversion
        $currency = CurrencyService::getCurrentCurrency($authSeller);
        // Return the final price multiplied by the currency conversion rate
        return $value * $currency->value;
    }


    public function getIsFavoriteAttribute()
    {
        // get favourite status of this product
        $sellerAuth = Auth::guard("sellerApi")->user();
        $isInList = $sellerAuth
            ? $sellerAuth->favorites()->where('product_id', $this->id)->exists()
            : false;
        return $isInList;
    }

    public function getIsAvailableAttribute($value)
    {
        if ($this->quantity > 0) {
            return 1;
        }
        $directPurchaseActiveWithPriority = $this->directPurchase()
            ->where('status', GeneralStatusEnum::getStatusActive())
            ->whereHas('directPurchasePriorities')
            ->exists();
        return $directPurchaseActiveWithPriority ? 1 : 0;
    }

    public function starVotes()
    {
        return $this->ratings()
            ->select('product_id', 'stars', DB::raw('COUNT(*) as vote_count'))
            ->groupBy('product_id', 'stars');
    }


    public function getImageAttribute($value): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)){
            return $value;
        }
        elseif (isset($value) && $value != 'no-image.png'){
            return asset('/storage/uploads/products'). '/'.$value;
        }
        else{
            return config('services.cloudinary.default_image');
        }
    }

    public function scopeVendorFilter($query, $vendors)
    {
        $query->whereIn('vendor_id', $vendors);
    }

    public function scopeActive($query)
    {
        return $query->where('status', "active");
    }
    // Accessor to calculate profit_rate
    public function getProfitRateAttribute()
    {
        if ($this->wholesale_price && $this->price) {

            $profitRate = (($this->price - $this->wholesale_price) / $this->wholesale_price) * 100;
            return round($profitRate, 2); // Round to 2 decimal places
        }
        return null;
    }
    public function getCalcTaxTypeAttribute()
    {
        $valueAddedTax = $this->load('valueAddedTax.country');
        if ($this->tax_id && $valueAddedTax) {
            return ProductTaxType::getTypePartial();
        }else{
            return ProductTaxType::getTypeIncluded();
        }
    }
    public function getCalcTaxRateAttribute()
    {
        $valueAddedTax = $this->valueAddedTax;
        if ($valueAddedTax) {
            return $valueAddedTax->tax_rate;
        }else{
            return 0;
        }
    }
    public function getCalcTaxAmountAttribute()
    {
        $valueAddedTax = $this->valueAddedTax;
        if ($this->tax_type == 'percentage' && $valueAddedTax) {
            return $this->tax_amount * $valueAddedTax->tax_rate;
        }elseif ($this->tax_type == 'fixed' && $valueAddedTax) {
            return $this->cost_price * $valueAddedTax->tax_rate;
        }else{
            return 0;
        }
    }
    public function calcDiscount()
    {
        if ($this->wholesale_price && $this->price) {
            return $this->price - $this->wholesale_price;
        }

        return null;
    }
}
