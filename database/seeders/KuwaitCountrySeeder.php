<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KuwaitCountrySeeder extends Seeder
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
            'code' => 'KW',
            'flag' => 'kw.svg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Country Translations
        DB::table('country_translations')->insert([
            ['country_id' => $countryId, 'language_id' => $langArId, 'name' => 'الكويت'],
            ['country_id' => $countryId, 'language_id' => $langEnId, 'name' => 'Kuwait'],
        ]);

        // Regions and their respective cities
        $regions = [
            'Al Ahmadi Governorate' => [
                'ar' => 'محافظة الأحمدي',
                'cities' => [
                    'Al Ahmadi' => 'الأحمدي',
                    'Al Faḩāḩīl' => 'الفحيحيل',
                    'Al Finţās' => 'الفنطاس',
                    'Al Mahbūlah' => 'المهبولة',
                    'Al Manqaf' => 'المنقف',
                    'Al Wafrah' => 'الوفرة',
                    'Ar Riqqah' => 'الرقة',
                ]
            ],
            'Al Farwaniyah Governorate' => [
                'ar' => 'محافظة الفروانية',
                'cities' => [
                    'Al Farwānīyah' => 'الفروانية',
                    'Janūb as Surrah' => 'جنوب السرة',
                ]
            ],
            'Al Jahra Governorate' => [
                'ar' => 'محافظة الجهراء',
                'cities' => [
                    'Al Jahrā’' => 'الجهراء',
                ]
            ],
            'Capital Governorate' => [
                'ar' => 'محافظة العاصمة',
                'cities' => [
                    'Ad Dasmah' => 'الدسمة',
                    'Ar Rābiyah' => 'الرابية',
                    'Ash Shāmīyah' => 'الشامية',
                    'Az Zawr' => 'الزور',
                    'Kuwait City' => 'مدينة الكويت',
                ]
            ],
            'Hawalli Governorate' => [
                'ar' => 'محافظة حولي',
                'cities' => [
                    'Ar Rumaythīyah' => 'الرميثية',
                    'As Sālimīyah' => 'السالمية',
                    'Bayān' => 'بيان',
                    'Ḩawallī' => 'حولي',
                    'Salwá' => 'سلوى',
                ]
            ],
            'Mubarak Al-Kabeer Governorate' => [
                'ar' => 'محافظة مبارك الكبير',
                'cities' => [
                    'Abu Al Hasaniya' => 'أبو الحصانية',
                    'Abu Fatira' => 'أبو فطيرة',
                    'Al Funayţīs' => 'الفنيطيس',
                    'Al-Masayel' => 'المسيلة',
                    'Şabāḩ as Sālim' => 'صباح السالم',
                ]
            ]
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
