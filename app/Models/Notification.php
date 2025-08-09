<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Notification extends BaseModel
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [ 'type', 'notifiable_id', 'notifiable_type', 'data', 'read_at', 'created_at', 'updated_at' ];

    protected $casts = [
        'data' => 'array',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function setDataAttribute($data)
    {
        $this->attributes['data'] = json_encode($data);
    }

    public function getDataAttribute($value)
    {
        $locale = app()->getLocale();
        $langId = Language::where('code', $locale)->first()->id;
        $data = json_decode($value, true);
        return [
            'variables' => $data['variables'] ?? null,
            'details' => [
                'title' => $data['details'][$locale]['title'] ?? $data['en']['title'] ?? null,
                'body' => $data['details'][$locale]['body'] ?? $data['en']['body'] ?? null,
            ]
        ];
    }

}
