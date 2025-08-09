<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirebaseToken extends BaseModel
{
    use HasFactory;

    protected $fillable = ['token'];


    public function ownerable()
    {
        return $this->morphTo();
    }

}
