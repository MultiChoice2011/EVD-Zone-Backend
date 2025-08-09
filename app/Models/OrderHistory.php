<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;

class OrderHistory extends BaseModel
{
    use HasFactory;

    protected $table = 'order_history';

    protected $fillable = [
        'order_id',
        'status',
        'note',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }


}
