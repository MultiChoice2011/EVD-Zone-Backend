<?php

namespace App\Models;


use App\Helpers\OpenSslHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;

class OrderProductSerial extends Model
{

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'order_product_id',
        'product_serial_id',
        'is_encrypted',
        'serial',
        'scratching',
        'buying',
        'expiring'
    ];

    public $timestamps = false;

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    public function orderProduct(): BelongsTo
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productSerial(): BelongsTo
    {
        return $this->belongsTo(ProductSerial::class);
    }

    protected function setScratchingAttribute($value): void
    {
        $scratch = OpenSslHelper::encrypt($value);
        $this->attributes['scratching'] = $scratch;
    }

    protected function getScratchingAttribute($value): ?string
    {
        if ($this->is_encrypted){
            return OpenSslHelper::decrypt($value) ?? null;
        }
        else{
            return $value;
        }
    }

}
