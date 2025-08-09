<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerSession extends Model
{
    use HasFactory;
    protected $fillable = ['seller_id', 'device_type', 'device_id', 'token', 'created_at', 'expired_at'];

    public $timestamps = false;

    protected $dates = ['created_at', 'expired_at'];

    public function seller()
    {
        return $this->belongsTo(Seller::class,'seller_id');
    }


    public static function boot()
    {
        parent::boot();

        // Set `created_at` automatically when creating a new record
        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }
}
