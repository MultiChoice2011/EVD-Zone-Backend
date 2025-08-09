<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Integration extends BaseModel
{

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'model',
        'status',
    ];

    protected $appends = [
    ];

    public function keys(): HasMany
    {
        return $this->hasMany(IntegrationKey::class);
    }

    public function vendor(): HasOne
    {
        return $this->HasOne(Vendor::class);
    }



}
