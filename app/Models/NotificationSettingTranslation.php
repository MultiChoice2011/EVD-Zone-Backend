<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSettingTranslation extends Model
{
    protected $fillable = [
        'notification_setting_id',
        'language_id',
        'title',
    ];
    public $timestamps = false;

    public function notificationSetting()
    {
        return $this->belongsTo(NotificationSetting::class);
    }
}
