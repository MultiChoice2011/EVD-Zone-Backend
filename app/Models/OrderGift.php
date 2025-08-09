<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class OrderGift extends Model
{

    protected $fillable = [
        'order_id', 'recipient_name', 'recipient_email', 'image', 'description'
    ];

    public $timestamps = false;

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }


    public function getImageAttribute($value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL)){
            return $value;
        }
        elseif (isset($value) && $value != 'no-image.png'){
            return asset('/storage/uploads/orders'). '/'.$value;
        }
        else{
            return config('services.cloudinary.default_image');
        }
    }

}
