<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartProductOptionValue extends Model
{
    use HasFactory;
    protected $fillable = ['cart_product_id','cart_product_option_id','option_value_id'];

    public $timestamps = false;

    public function cartProduct(): BelongsTo
    {
        return $this->belongsTo(CartProduct::class,'cart_product_id');
    }

    public function cartProductOption(): BelongsTo
    {
        return $this->belongsTo(CartProductOption::class,'cart_product_option_id');
    }

    public function optionValue(): BelongsTo
    {
        return $this->belongsTo(OptionValue::class,'option_value_id');
    }
}
