<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\NotificationSetting;
use App\Models\NotificationSettingTranslation;
use App\Models\Setting;
use App\Models\SettingTranslation;
use Illuminate\Database\Seeder;

class NotificationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = Language::all();
        $notificationSettings = [
            'new_orders' => 'new orders',
            'store_rate' => 'store rate',
            'product_rate' => 'product rate',
            'minimum_stock' => 'minimum stock',
        ];

        foreach ($notificationSettings as $key => $notificationSetting){
            $notification = NotificationSetting::create([
                'key' => $key,
                'notification_app' => 1,
                'notification_email' => 1,
            ]);
            foreach ($languages as $language) {
                $languageId = $language->id;
                NotificationSettingTranslation::create([
                    'notification_setting_id' => $notification->id,
                    'language_id' => $languageId,
                    'title' => $notificationSetting,
                ]);
            }
        }

    }
}
