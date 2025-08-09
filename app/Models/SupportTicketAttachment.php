<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SupportTicketAttachment extends BaseModel
{
    use HasFactory;
    protected $fillable = ['support_ticket_id','file_url','extension','size'];
    public function supportTicket() : BelongsToMany
    {
        return $this->belongsToMany(SupportTicket::class,'support_ticket_id');
    }
    public function getfileUrlAttribute($value): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)){
            return $value;
        }
        elseif (isset($value) && $value != 'no-image.png'){
            return asset('/storage/uploads/supportTickets'). '/'.$value;
        }
        else{
            return config('services.cloudinary.default_image');
        }
    }
}
