<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BahrainCountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $langArId = 1;
        $langEnId = 2;

        // Insert Saudi Arabia Country
        $countryId = DB::table('countries')->insertGetId([
            'code' => 'BH',
            'flag' => 'bh.svg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Country Translations
        DB::table('country_translations')->insert([
            ['country_id' => $countryId, 'language_id' => $langArId, 'name' => 'البحرين'],
            ['country_id' => $countryId, 'language_id' => $langEnId, 'name' => 'Bahrain'],
        ]);

        // Regions and their respective cities
        $regions = [
            'Capital Governorate' => [
                'ar' => 'المحافظة العاصمة',
                'cities' => [
                    'Jidd Ḩafş' => 'جد حفص',
                    'Manama' => 'المنامة',
                    'Sitrah' => 'سترة',
                ],
            ],
            'Central Governorate' => [
                'ar' => 'المحافظة الوسطى',
                'cities' => [
                    'Madīnat Ḩamad' => 'مدينة حمد',
                ],
            ],
            'Muharraq Governorate' => [
                'ar' => 'محافظة المحرق',
                'cities' => [
                    'Al Ḩadd' => 'الحد',
                    'Al Muharraq' => 'المحرق',
                ],
            ],
            'Northern Governorate' => [
                'ar' => 'المحافظة الشمالية',
                'cities' => [
                    'Northern Governorate' => 'المحافظة الشمالية',
                ],
            ],
            'Southern Governorate' => [
                'ar' => 'المحافظة الجنوبية',
                'cities' => [
                    'Ar Rifā‘' => 'الرفاع',
                    'Dār Kulayb' => 'دار كليب',
                    'Madīnat ‘Īsá' => 'مدينة عيسى',
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
