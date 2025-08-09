<?php

namespace App\Repositories\Admin;

use App\Helpers\FileUpload;
use App\Models\Language;
use App\Models\NotificationSetting;
use App\Models\Setting;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class NotificationSettingRepository extends BaseRepository
{
    use FileUpload;

    public function __construct(
        Application $app,
    )
    {
        parent::__construct($app);
    }


    public function index()
    {
        return $this->model
            //->with('translations')
            ->get();
    }

    public function updateNotificationSettings($id, $requestData)
    {
        $setting = $this->model->where('id',$id)->first();
        if (! $setting)
            return false;
        $setting->{$requestData->key} = $requestData->value;
        $setting->save();
        return $setting;
    }


    public function model(): string
    {
        return NotificationSetting::class;
    }
}
