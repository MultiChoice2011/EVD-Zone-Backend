<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodeVerification extends BaseModel
{

    protected $fillable = [
        'verifiable_type',
        'verifiable_id',
        'code',
        'type',
        'token',
        'is_id',
        'used',
        'expire_at'
    ];

    public function verifiable()
    {
        return $this->morphTo();
    }
}
