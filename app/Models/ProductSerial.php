<?php

namespace App\Models;

use App\Helpers\OpenSslHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductSerial extends BaseModel
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_id',
        'product_id',
        'is_encrypted',
        'serial',
        'scratching',
        'status',
        'buying',
        'expiring',
        'price_before_vat',
        'vat_amount',
        'price_after_vat',
        'currency',
    ];


    public function invoice(): BelongsTo
    {
        return $this->BelongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function getFileAttribute($value): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)){
            return $value;
        }
        elseif (isset($value)){
            return asset('storage/uploads/products' . '/' . $value);
        }
        else{
            return config('services.cloudinary.default_image');
        }
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
