<?php

namespace App\Models;

use App\Enums\GeneralStatusEnum;
use App\Helpers\FileUpload;
use App\Services\General\CurrencyService;
use App\Traits\TranslatedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Seller extends Authenticatable implements JWTSubject
{
    use SoftDeletes, TranslatedAttributes,Notifiable,HasRoles,FileUpload;

    protected $table = 'sellers';

    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
        'owner_name',
        'email',
//        'password',
        'status',
        'approval_status',
        'logo',
        'phone',
        'balance',
        'enable_google2fa',
        'google2fa_secret',
        'address_details',
        'seller_group_id',
        'seller_group_level_id',
        'otp',
        'commercial_register_number',
        'tax_card_number',
        'currency_id',
        'verify'
    ];
    protected $hidden = ['password'];

    public $translatedAttributes = ['reject_reason'];

    protected $appends = ['approval_status_form'];

    public function orders()
    {
        return $this->morphMany(Order::class, 'ownerable');
    }

    public function cart(): MorphOne
    {
        return $this->MorphOne(Cart::class, 'owner');
    }

    public function rolesCreated(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(SellerTranslations::class);
    }
    public function seller_transactions(): HasMany
    {
        return $this->hasMany(SellerTransaction::class);
    }

    public function rejectReasons()
    {
        return $this->hasMany(SellerRejectReason::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function sellerGroup(): BelongsTo
    {
        return $this->belongsTo(SellerGroup::class, 'seller_group_id');
    }
    public function sellerGroupLevel(): BelongsTo
    {
        return $this->belongsTo(SellerGroupLevel::class, 'seller_group_level_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function sellerAttachment(): HasMany
    {
        return $this->hasMany(SellerAttachment::class, 'seller_id');
    }
    public function balanceHistories()
    {
        return $this->morphMany(BalanceHistory::class, 'ownerble');
    }
    public function sellerAddress(): HasOne
    {
        return $this->hasOne(SellerAddress::class);
    }
    // Define the polymorphic relationship to favorites
    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }
    public function firebaseTokens()
    {
        return $this->morphMany(FirebaseToken::class, 'ownerable');
    }
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
    public function pointsHistory()
    {
        return $this->morphMany(PointsHistory::class, 'pointable');
    }

    public function SupportTickets() : MorphMany
    {
        return $this->morphMany(SupportTicket::class,'customer');
    }
    public function getLogoAttribute($value): string
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
    protected function getApprovalStatusFormAttribute()
    {
        return $this->approval_status ? __('constants.' . $this->approval_status) : '';
    }
    protected function getBalanceAttribute($value)
    {
        $authSeller = auth('sellerApi')->user();
        // Default to the original price
        $finalAmount = $value;
        // Now apply the currency conversion
        $currency = CurrencyService::getCurrentCurrency($authSeller);
        // Ensure currency is valid and has the 'value' property
        $conversionRate = $currency->value ?? 1;
        // Return the final price multiplied by the currency conversion rate
        if ($this->parent) {
            return $this->relationLoaded('parent')
                ? $this->parent->balance
                : $this->load('parent')->parent->balance;
        }
        return $finalAmount * $conversionRate;
    }
    protected function setBalanceAttribute($value)
    {
        if ($this->parent) {
            // Update the parent's balance
            $this->parent->update(['balance' => $value]);
        } else {
            // Update the current seller balance
            $this->attributes['balance'] = $value;
        }
    }


    public function scopeActive($query)
    {
        return $query->where('status', GeneralStatusEnum::getStatusActive());
    }
    public function scopeApproved($query)
    {
        return $query->where('approval_status','approved');
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function roles(): BelongsToMany
    {
        $relation = $this->morphToMany(
            Role::class,
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.model_morph_key'),
            app(PermissionRegistrar::class)->pivotRole
        );

        if (! app(PermissionRegistrar::class)->teams) {
            return $relation;
        }

        $teamField = config('permission.table_names.roles').'.'.app(PermissionRegistrar::class)->teamsKey;

        return $relation->wherePivot(app(PermissionRegistrar::class)->teamsKey, getPermissionsTeamId())
            ->where(fn ($q) => $q->whereNull($teamField)->orWhere($teamField, getPermissionsTeamId()));
    }
    /**
     * A model may have multiple direct permissions.
     */
    public function permissions(): BelongsToMany
    {
        $relation = $this->morphToMany(
            Permission::class,
            'model',
            config('permission.table_names.model_has_permissions'),
            config('permission.column_names.model_morph_key'),
            app(PermissionRegistrar::class)->pivotPermission
        );

        if (! app(PermissionRegistrar::class)->teams) {
            return $relation;
        }

        $teamsKey = app(PermissionRegistrar::class)->teamsKey;
        $relation->withPivot($teamsKey);

        return $relation->wherePivot($teamsKey, getPermissionsTeamId());
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    protected function getDefaultGuardName(): string { return 'sellerApi'; }

}
