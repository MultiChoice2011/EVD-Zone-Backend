<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class CartProductOption extends Model
{
    use HasFactory;
    protected $fillable = ['cart_product_id','product_option_id','option_value_id','value'];
    public $timestamps = false;

    public function cartProduct(): BelongsTo
    {
        return $this->belongsTo(CartProduct::class,'cart_product_id');
    }

    public function productOption(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class,'product_option_id');
    }

    public function optionDetails(): HasOneThrough
    {
        return $this->hasOneThrough(Option::class, ProductOption::class, 'id', 'id', 'product_option_id', 'option_id');
    }

    public function cartProductOptionValues(): HasMany
    {
        return $this->hasMany(CartProductOptionValue::class);
    }
}
