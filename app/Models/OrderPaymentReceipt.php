<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPaymentReceipt extends Model
{
    use HasFactory;
    protected $fillable =['order_id','file_path','public_id'];
    public function order() :BelongsTo
    {
        return $this->belongsTo(Order::class,'order_id');
    }
    public function getFilePathAttribute($value): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)){
            return $value;
        }
        else{
            return config('services.cloudinary.default_image');
        }
    }
}
