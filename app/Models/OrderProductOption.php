<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class OrderProductOption extends Model
{
    protected $table = "order_product_options";
    protected $fillable = [
        'order_product_id', 'product_option_id', 'value'
    ];

    public $timestamps = false;

    public function orderProduct(): BelongsTo
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function productOption(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class);
    }

    public function optionDetails(): HasOneThrough
    {
        return $this->hasOneThrough(Option::class, ProductOption::class, 'id', 'id', 'product_option_id', 'option_id');
    }

    public function optionValues(): HasMany
    {
        return $this->hasMany(OrderProductOptionValue::class);
    }

}
