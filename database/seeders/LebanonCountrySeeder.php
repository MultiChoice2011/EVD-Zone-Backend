<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LebanonCountrySeeder extends Seeder
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
            'code' => 'LB',
            'flag' => 'lb.svg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Country Translations
        DB::table('country_translations')->insert([
            ['country_id' => $countryId, 'language_id' => $langArId, 'name' => 'لبنان'],
            ['country_id' => $countryId, 'language_id' => $langEnId, 'name' => 'Lebanon'],
        ]);

        // Regions and their respective cities
        $regions = [
            "Akkar Governorate" => [
                "ar" => "محافظة عكار",
                "cities" => [
                    "Caza de Aakkar" => "قضاء عكار",
                ],
            ],
            "Baalbek-Hermel Governorate" => [
                "ar" => "محافظة بعلبك - الهرمل",
                "cities" => [
                    "Baalbek" => "بعلبك",
                    "Caza de Baalbek" => "قضاء بعلبك",
                ],
            ],
            "Beirut Governorate" => [
                "ar" => "محافظة بيروت",
                "cities" => [
                    "Beirut" => "بيروت",
                    "Ra’s Bayrūt" => "رأس بيروت",
                ],
            ],
            "Beqaa Governorate" => [
                "ar" => "محافظة البقاع",
                "cities" => [
                    "Aanjar" => "عنجر",
                    "Zahlé" => "زحلة",
                ],
            ],
            "Mount Lebanon Governorate" => [
                "ar" => "محافظة جبل لبنان",
                "cities" => [
                    "Baabda" => "بعبدا",
                    "Bhamdoun" => "بحمدون",
                    "Bhamdoûn el Mhatta" => "بحمدون المحطة",
                    "Caza de Baabda" => "قضاء بعبدا",
                    "Jbaïl" => "جبيل",
                    "Jounieh" => "جونيه",
                ],
            ],
            "Nabatieh Governorate" => [
                "ar" => "محافظة النبطية",
                "cities" => [
                    "Ain Ebel" => "عين إبل",
                    "Caza de Bent Jbaïl" => "قضاء بنت جبيل",
                    "Caza de Nabatîyé" => "قضاء النبطية",
                    "Habboûch" => "حبوش",
                    "Marjayoûn" => "مرجعيون",
                    "Nabatîyé et Tahta" => "النبطية التحتا",
                ],
            ],
            "North Governorate" => [
                "ar" => "محافظة الشمال",
                "cities" => [
                    "Batroûn" => "البترون",
                    "Bcharré" => "بشري",
                    "Tripoli" => "طرابلس",
                ],
            ],
            "South Governorate" => [
                "ar" => "محافظة الجنوب",
                "cities" => [
                    "En Nâqoûra" => "الناقورة",
                    "Ghazieh" => "غزّة",
                    "Sidon" => "صيدا",
                    "Tyre" => "صور",
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
