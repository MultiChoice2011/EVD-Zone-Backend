<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SupportTicket extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'customer_type',
        'customer_id',
        'subseller_id',
        'title',
        'order_id',
        'details',
        'status',
        'error_message'
    ];
    public function customer() : MorphTo
    {
        return $this->morphTo();
    }
    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class,'order_id');
    }
    public function supportTicketsAttachments() : HasOne
    {
        return $this->hasOne(SupportTicketAttachment::class,'support_ticket_id');
    }
    public function replies() : HasMany
    {
        return $this->hasMany(SupportTicketReplay::class,'support_ticket_id');
    }
    public function scopePending($query)
    {
        return $query->whereStatus('pending');
    }
    public function scopeCompleted($query)
    {
        return $query->whereStatus('complete');
    }
}
