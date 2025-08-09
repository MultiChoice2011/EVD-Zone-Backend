<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorAttachment extends BaseModel
{
    protected $fillable = [
        'vendor_id',
        'file_url',
        'extension',
        'size',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function getFileUrlAttribute($value): string|null
    {
        if ($value == null || filter_var($value, FILTER_VALIDATE_URL)){
            return $value;
        }
        elseif (isset($value)){
            return asset('storage/uploads/vendors' . '/' . $value);
        }
        else{
            return config('services.cloudinary.default_image');
        }
    }





}
