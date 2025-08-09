<?php

namespace App\Models;


use App\Enums\VendorStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Vendor extends BaseModel
{

    use SoftDeletes, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'integration_id',
        'country_id',
        'name',
        'logo',
        'status',
        'is_service',
        'street',
        'serial_number',
        'phone',
        'email',
        'description',
        'owner_name',
        'web',
        'mobile',
        'city_id',
        'region_id',
    ];

    public function integration(): BelongsTo
    {
        return $this->BelongsTo(Integration::class);
    }
    public function attachments(): HasMany
    {
        return $this->hasMany(VendorAttachment::class, 'vendor_id');
    }
    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function region(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function city(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(Product::class);
    }

    public function VendorProducts(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(VendorProduct::class);
    }


    public function getLogoAttribute($value): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)){
            return $value;
        }
        elseif (isset($value)){
            return asset('storage/uploads/vendors' . '/' . $value);
        }
        else{
            return config('services.cloudinary.default_image');
        }
    }

    public function getImageAttachAttribute($value): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)){
            return $value;
        }
        elseif (isset($value)){
            return asset('storage/uploads/vendors' . '/' . $value);
        }
        else{
            return config('services.cloudinary.default_image');
        }
    }



    public function scopeApproved($query)
    {
        $query->where('status', VendorStatus::getTypeApproved());
    }

}
