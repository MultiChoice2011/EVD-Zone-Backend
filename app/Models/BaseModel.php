<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function getCreatedAtAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }
        return Carbon::parse($value)->setTimezone(config('app.timezone'))->setTimezone(config('app.timezone'))
            ->format('Y-m-d\TH:i:s.u\Z');
    }

    public function getUpdatedAtAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }
        return Carbon::parse($value)->setTimezone(config('app.timezone'))->setTimezone(config('app.timezone'))
            ->format('Y-m-d\TH:i:s.u\Z');
    }

    public function getDeletedAtAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }
        return Carbon::parse($value)
            ->setTimezone(config('app.timezone'))
            ->format('Y-m-d\TH:i:s.u\Z');
    }
}
