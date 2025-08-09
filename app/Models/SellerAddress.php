<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerAddress extends Model
{
    protected $table = 'seller_address';
    protected $fillable = [
        'seller_id',
        'country_id',
        'city_id',
        'region_id',
        'street',
    ];

    public $timestamps = false;

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }





}
