<?php

namespace App\Models;

use App\Helpers\OpenSslHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegrationKey extends Model
{

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'integration_id',
        'key',
        'value',
    ];

    public $timestamps = false;

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }

    protected function getValueAttribute($value): ?string
    {
        return OpenSslHelper::decrypt($value) ?? null;
    }


}
