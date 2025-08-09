<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'status',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];




    public function getAvatarAttribute($value): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)){
            return $value;
        }
        elseif (isset($value) && $value != 'no-image.png'){
            return asset('/storage/uploads/admins'). '/'.$value;
        }
        else{
            return config('services.cloudinary.default_image');
        }
    }




    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function firebaseTokens()
    {
        return $this->morphMany(FirebaseToken::class, 'ownerable');
    }


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








    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected function getDefaultGuardName(): string { return 'adminApi'; }
}
