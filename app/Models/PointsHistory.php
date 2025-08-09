<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class PointsHistory extends BaseModel
{
    protected $table = 'points_history';
    protected $fillable = [
        'points',
        'pointable_id',
        'pointable_type',
    ];

    public function pointable()
    {
        return $this->morphTo();
    }

}
