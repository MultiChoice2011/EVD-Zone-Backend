<?php

namespace App\Models;

use App\Traits\TranslatedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationSetting extends BaseModel
{
    use TranslatedAttributes;

    protected $fillable = ['notification_app', 'notification_email'];

    public $translatedAttributes = [
        'title',
    ];


    public function translations(): HasMany
    {
        return $this->hasMany(NotificationSettingTranslation::class);
    }


}
