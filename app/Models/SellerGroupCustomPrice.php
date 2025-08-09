<?php

namespace App\Models;

use App\Traits\TranslatesName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SellerGroupCustomPrice extends BaseModel
{

    use TranslatesName;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'model_id',
        'seller_group_id',
        'advantage',
        'type',
        'amount',
    ];

    public function seller_group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SellerGroup::class);
    }


}
