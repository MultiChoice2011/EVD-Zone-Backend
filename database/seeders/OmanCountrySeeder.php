<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OmanCountrySeeder extends Seeder
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
            'code' => 'OM',
            'flag' => 'om.svg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Country Translations
        DB::table('country_translations')->insert([
            ['country_id' => $countryId, 'language_id' => $langArId, 'name' => 'عمان'],
            ['country_id' => $countryId, 'language_id' => $langEnId, 'name' => 'Oman'],
        ]);

        // Regions and their respective cities
        $regions = [
            "Ad Dakhiliyah Governorate" => [
                "ar" => "محافظة الداخلية",
                "cities" => [
                    "Adam" => "آدم",
                    "Bahlā’" => "بهلاء",
                    "Bidbid" => "بدبد",
                    "Izkī" => "إزكي",
                    "Nizwá" => "نزوى",
                    "Sufālat Samā’il" => "سفالة سمائل",
                ],
            ],
            "Ad Dhahirah Governorate" => [
                "ar" => "محافظة الظاهرة",
                "cities" => [
                    "‘Ibrī" => "عبري",
                    "Yanqul" => "ينقل",
                ],
            ],
            "Al Batinah North Governorate" => [
                "ar" => "محافظة الباطنة شمال",
                "cities" => [
                    "Al Khābūrah" => "الخابورة",
                    "As Suwayq" => "السويق",
                    "Liwá" => "اللوى",
                    "Şaḩam" => "صحم",
                    "Shināş" => "شناص",
                    "Sohar" => "صُحار",
                ],
            ],
            "Al Batinah Region" => [
                "ar" => "منطقة الباطنة",
                "cities" => [
                    "Barkā’" => "بركاء",
                    "Bayt al ‘Awābī" => "بيت العوابي",
                    "Oman Smart Future City" => "مدينة عمان الذكية المستقبلية",
                    "Rustaq" => "الرستاق",
                ],
            ],
            "Al Batinah South Governorate" => [
                "ar" => "محافظة الباطنة جنوب",
                "cities" => [
                    "Al Batinah South" => "الباطنة جنوب",
                ],
            ],
            "Al Buraimi Governorate" => [
                "ar" => "محافظة البريمي",
                "cities" => [
                    "Al Buraymī" => "البريمي",
                ],
            ],
            "Al Wusta Governorate" => [
                "ar" => "محافظة الوسطى",
                "cities" => [
                    "Haymā’" => "هيما",
                ],
            ],
            "Ash Sharqiyah North Governorate" => [
                "ar" => "محافظة الشرقية شمال",
                "cities" => [
                    "Ash Sharqiyah North" => "الشرقية شمال",
                ],
            ],
            "Ash Sharqiyah Region" => [
                "ar" => "منطقة الشرقية",
                "cities" => [
                    "Sur" => "صور",
                ],
            ],
            "Ash Sharqiyah South Governorate" => [
                "ar" => "محافظة الشرقية جنوب",
                "cities" => [
                    "Ash Sharqiyah South" => "الشرقية جنوب",
                ],
            ],
            "Dhofar Governorate" => [
                "ar" => "محافظة ظفار",
                "cities" => [
                    "Şalālah" => "صلالة",
                ],
            ],
            "Musandam Governorate" => [
                "ar" => "محافظة مسندم",
                "cities" => [
                    "Dib Dibba" => "دب دب",
                    "Khasab" => "خصب",
                    "Madḩā’ al Jadīdah" => "مذحى الجديدة",
                ],
            ],
            "Muscat Governorate" => [
                "ar" => "محافظة مسقط",
                "cities" => [
                    "Bawshar" => "بوشار",
                    "Muscat" => "مسقط",
                    "Seeb" => "السيب",
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
