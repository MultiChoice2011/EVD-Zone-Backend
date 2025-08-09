<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitedArabEmiratesCountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $langArId = 1;
        $langEnId = 2;

        // Insert United Arab Emirates Country
        $countryId = DB::table('countries')->insertGetId([
            'code' => 'AE',
            'flag' => 'ae.svg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Country Translations
        DB::table('country_translations')->insert([
            ['country_id' => $countryId, 'language_id' => $langArId, 'name' => 'الإمارات العربية المتحدة'],
            ['country_id' => $countryId, 'language_id' => $langEnId, 'name' => 'United Arab Emirates'],
        ]);

        // Regions and their respective cities
        $regions = [
            'Abu Dhabi Emirate' => [
                'ar' => 'إمارة أبوظبي',
                'cities' => [
                    'Abu Dhabi Island and Internal Islands City' => 'مدينة أبوظبي وجزرها الداخلية',
                    'Abu Dhabi Municipality' => 'بلدية أبوظبي',
                    'Al Ain City' => 'مدينة العين',
                    'Al Ain Municipality' => 'بلدية العين',
                    'Al Dhafra' => 'الظفرة',
                    'Al Shamkhah City' => 'مدينة الشامخة',
                    'Ar Ruways' => 'الرويس',
                    'Bani Yas City' => 'مدينة بني ياس',
                    'Khalifah A City' => 'مدينة خليفة أ',
                    'Musaffah' => 'مصفح',
                    'Muzayri‘' => 'مزرع',
                    'Zayed City' => 'مدينة زايد',
                ],
            ],
            'Ajman Emirate' => [
                'ar' => 'إمارة عجمان',
                'cities' => [
                    'Ajman' => 'عجمان',
                    'Ajman City' => 'مدينة عجمان',
                    'Manama' => 'منامة',
                    'Masfout' => 'مصفوت',
                ],
            ],
            'Dubai' => [
                'ar' => 'دبي',
                'cities' => [
                    'Dubai' => 'دبي',
                ],
            ],
            'Fujairah' => [
                'ar' => 'الفجيرة',
                'cities' => [
                    'Al Fujairah City' => 'مدينة الفجيرة',
                    'Al Fujairah Municipality' => 'بلدية الفجيرة',
                    'Dibba Al Fujairah Municipality' => 'بلدية دبا الفجيرة',
                    'Dibba Al-Fujairah' => 'دبا الفجيرة',
                    'Dibba Al-Hisn' => 'دبا الحصن',
                    'Reef Al Fujairah City' => 'مدينة ريف الفجيرة',
                ],
            ],
            'Ras al-Khaimah' => [
                'ar' => 'رأس الخيمة',
                'cities' => [
                    'Ras Al Khaimah' => 'رأس الخيمة',
                    'Ras Al Khaimah City' => 'مدينة رأس الخيمة',
                ],
            ],
            'Sharjah Emirate' => [
                'ar' => 'إمارة الشارقة',
                'cities' => [
                    'Adh Dhayd' => 'الذيد',
                    'Al Batayih' => 'البطائح',
                    'Al Hamriyah' => 'الحمرية',
                    'Al Madam' => 'المدام',
                    'Dhaid' => 'الذيد',
                    'Dibba Al Hesn' => 'دبا الحصن',
                    'Kalba' => 'كلباء',
                    'Khawr Fakkān' => 'خورفكان',
                    'Khor Fakkan' => 'خورفكان',
                    'Milehah' => 'مليحة',
                    'Murbaḩ' => 'مربح',
                    'Sharjah' => 'الشارقة',
                ],
            ],
            'Umm al-Quwain' => [
                'ar' => 'أم القيوين',
                'cities' => [
                    'Umm AL Quwain' => 'أم القيوين',
                    'Umm Al Quwain City' => 'مدينة أم القيوين',
                ],
            ],
        ];

        // Insert regions and their translations
        foreach ($regions as $region_en => $region_data) {
            $regionId = DB::table('regions')->insertGetId([
                'country_id' => $countryId,
            ]);

            DB::table('region_translations')->insert([
                ['region_id' => $regionId, 'language_id' => $langArId, 'name' => $region_data['ar']],
                ['region_id' => $regionId, 'language_id' => $langEnId, 'name' => $region_en],
            ]);

            // Insert cities and their translations
            foreach ($region_data['cities'] as $city_en => $city_ar) {
                $cityId = DB::table('cities')->insertGetId([
                    'region_id' => $regionId,
                ]);

                DB::table('city_translations')->insert([
                    ['city_id' => $cityId, 'language_id' => $langArId, 'name' => $city_ar],
                    ['city_id' => $cityId, 'language_id' => $langEnId, 'name' => $city_en],
                ]);
            }
        }
    }
}
