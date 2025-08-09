<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerAttachment extends BaseModel
{
    protected $table = 'seller_attachments';
    protected $fillable = [
        'seller_id',
        'file_url',
        'type',
        'extension',
        'size',
    ];
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function getFileUrlAttribute($value): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)){
            return $value;
        }
        elseif (isset($value) && $value != 'no-image.png'){
            return asset('/storage/uploads/sellers'). '/'.$value;
        }
        else{
            return config('services.cloudinary.default_image');
        }
    }





}
