<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class OrderProductOptionValue extends Model
{
    protected $table = "order_product_option_values";
    protected $fillable = [
        'order_product_id', 'order_product_option_id', 'option_value_id'
    ];

    public $timestamps = false;

    public function orderProduct(): BelongsTo
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function orderProductOption(): BelongsTo
    {
        return $this->belongsTo(OrderProductOption::class);
    }

    public function optionValueDetails(): BelongsTo
    {
        return $this->belongsTo(OptionValue::class, 'option_value_id');
    }

}
