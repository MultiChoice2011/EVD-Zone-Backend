<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QatarCountrySeeder extends Seeder
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
            'code' => 'QA',
            'flag' => 'qa.svg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Country Translations
        DB::table('country_translations')->insert([
            ['country_id' => $countryId, 'language_id' => $langArId, 'name' => 'قطر'],
            ['country_id' => $countryId, 'language_id' => $langEnId, 'name' => 'Qatar'],
        ]);

        // Regions and their respective cities
        $regions = [
            'Al Daayen' => [
                'ar' => 'الضعاين',
                'cities' => [
                    'Al Daayen' => 'الضعاين',
                ],
            ],
            'Al Khor' => [
                'ar' => 'الخُور',
                'cities' => [
                    'Al Ghuwayriyah' => 'الغويرية',
                    'Al Khawr' => 'الخُور',
                ],
            ],
            'Al Rayyan Municipality' => [
                'ar' => 'بلدية الريان',
                'cities' => [
                    'Ar Rayyan' => 'الريان',
                    'Umm Bab' => 'أم باب',
                ],
            ],
            'Al Wakrah' => [
                'ar' => 'الوكرة',
                'cities' => [
                    'Al Wakrah' => 'الوكرة',
                    'Al Wukayr' => 'الوكير',
                    'Musayid' => 'مسيعيد',
                ],
            ],
            'Al-Shahaniya' => [
                'ar' => 'الشحانية',
                'cities' => [
                    'Al Jumayliyah' => 'الجُميليّة',
                    'Ash Shīḩānīyah' => 'الشحانية',
                    'Dukhan' => 'دخان',
                ],
            ],
            'Doha' => [
                'ar' => 'الدوحة',
                'cities' => [
                    'Doha' => 'الدوحة',
                ],
            ],
            'Madinat ash Shamal' => [
                'ar' => 'مدينة الشمال',
                'cities' => [
                    'Ar Ruways' => 'الرويس',
                    'Fuwayriţ' => 'فويرط',
                    'Madīnat ash Shamāl' => 'مدينة الشمال',
                ],
            ],
            'Umm Salal Municipality' => [
                'ar' => 'بلدية أم صلال',
                'cities' => [
                    'Umm Şalāl Muḩammad' => 'أم صلال محمد',
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
