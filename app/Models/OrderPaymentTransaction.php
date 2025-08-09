<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPaymentTransaction extends BaseModel
{

    use HasFactory;


    protected $fillable = [
        'order_id',
        'customer_id',
        'reference_number',
        'payment_id',
        'payment_type',
        'amount',
        'currency',
        'paymentBrand',
        'last_4_digits',
    ];


    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }


}
