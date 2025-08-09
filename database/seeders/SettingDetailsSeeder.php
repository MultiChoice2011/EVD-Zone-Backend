<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Setting;
use App\Models\SettingTranslation;
use Illuminate\Database\Seeder;

class SettingDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::insert([
            [
                'key' => 'phone',
                'is_translatable' => 0,
                'plain_value' => '+201111111111',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'email',
                'is_translatable' => 0,
                'plain_value' => 'multi-choice@suport.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'main_image',
                'is_translatable' => 0,
                'plain_value' => 'image-0.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'maintenance_mode',
                'is_translatable' => 0,
                'plain_value' => '0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'otp_6_digit',
                'is_translatable' => 0,
                'plain_value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'sms_verification_type',
                'is_translatable' => 0,
                'plain_value' => 'msegat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'dashboard_default_language',
                'is_translatable' => 0,
                'plain_value' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'website_default_language',
                'is_translatable' => 0,
                'plain_value' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'country_id',
                'is_translatable' => 0,
                'plain_value' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'city_id',
                'is_translatable' => 0,
                'plain_value' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'region_id',
                'is_translatable' => 0,
                'plain_value' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_id',
                'is_translatable' => 0,
                'plain_value' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // [
            //     'key' => 'time_zone',
            //     'is_translatable' => 0,
            //     'plain_value' => 'China Standard Time (UTC+08:00)',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            [
                'key' => 'postal_code',
                'is_translatable' => 0,
                'plain_value' => '12345',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'tax_number',
                'is_translatable' => 0,
                'plain_value' => '315454',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'show_tax_number',
                'is_translatable' => 0,
                'plain_value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'tax_files',
                'is_translatable' => 0,
                'plain_value' => '["sdsd.png", "qwaa.png"]',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'prices_include_tax',
                'is_translatable' => 0,
                'plain_value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);



        $languages = Language::all();
        $settingsTranslatedArray = [
            'store_name',
            'manager_name',
            'address_line',
            'meta_title',
            'meta_description',
            'meta_keywords',
        ];

        foreach ($settingsTranslatedArray as $settingTrans){
            $setting = Setting::create([
                'key' => $settingTrans,
                'is_translatable' => '1',
            ]);
            foreach ($languages as $language) {
                $languageId = $language->id;
                SettingTranslation::create([
                    'setting_id' => $setting->id,
                    'value' => 'test',
                    'language_id' => $languageId,
                ]);
            }
        }

    }
}
