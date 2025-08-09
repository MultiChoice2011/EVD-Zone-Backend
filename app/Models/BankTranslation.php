<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTranslation extends Model
{
    use HasFactory;
    protected $fillable = ['name','description','bank_id','language_id'];
    public function language(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Language::class,'language_id');
    }
    public function bank(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Bank::class,'bank_id');
    }
}
