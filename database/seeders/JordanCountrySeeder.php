<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JordanCountrySeeder extends Seeder
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
            'code' => 'JO',
            'flag' => 'jo.svg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Country Translations
        DB::table('country_translations')->insert([
            ['country_id' => $countryId, 'language_id' => $langArId, 'name' => 'الاردن'],
            ['country_id' => $countryId, 'language_id' => $langEnId, 'name' => 'Jordan'],
        ]);

        // Regions and their respective cities
        $regions = [
            "Ajloun Governorate" => [
                "ar" => "محافظة عجلون",
                "cities" => [
                    "‘Ajlūn" => "عجلون",
                    "‘Anjarah" => "عنجره",
                    "‘Ayn Jannah" => "عين جنة",
                    "Ḩalāwah" => "حلاوة",
                    "Şakhrah" => "صخرة",
                ],
            ],
            "Amman Governorate" => [
                "ar" => "محافظة عمان",
                "cities" => [
                    "Al Jīzah" => "الجِيزَة",
                    "Al Jubayhah" => "الجبيهة",
                    "Amman" => "عمان",
                    "Ḩayy al Bunayyāt" => "حي البنيات",
                    "Ḩayy al Quwaysimah" => "حي القويسمة",
                    "Jāwā" => "جاوا",
                    "Saḩāb" => "سحاب",
                    "Umm as Summāq" => "أم السماق",
                    "Wādī as Sīr" => "وادي السير",
                ],
            ],
            "Aqaba Governorate" => [
                "ar" => "محافظة العقبة",
                "cities" => [
                    "Aqaba" => "العقبة",
                    "Tala Bay" => "تالا باي",
                ],
            ],
            "Balqa Governorate" => [
                "ar" => "محافظة البلقاء",
                "cities" => [
                    "Al Karāmah" => "الكرامة",
                    "As Salţ" => "السلط",
                    "Yarqā" => "يرقا",
                ],
            ],
            "Irbid Governorate" => [
                "ar" => "محافظة إربد",
                "cities" => [
                    "Ar Ramthā" => "الرمثا",
                    "Ash Shajarah" => "الشجرة",
                    "Aţ Ţayyibah" => "الطيبة",
                    "Aţ Ţurrah" => "الطرة",
                    "Aydūn" => "ايدون",
                    "Bayt Īdis" => "بيت إيدس",
                    "Bayt Yāfā" => "بيت يافا",
                    "Dayr Yūsuf" => "دير يوسف",
                    "Ḩakamā" => "حكما",
                    "Ḩātim" => "حاتم",
                    "Irbid" => "إربد",
                    "Judita" => "جدّيتا",
                    "Kafr Abīl" => "كفر عبيّل",
                    "Kafr Asad" => "كفر أسد",
                    "Kafr Sawm" => "كفر سوم",
                    "Kharjā" => "خرجا",
                    "Kitim" => "كتم",
                    "Kurayyimah" => "كريمه",
                    "Malkā" => "ملكا",
                    "Qumaym" => "قميم",
                    "Saḩam al Kaffārāt" => "سحم الكفارات",
                    "Sāl" => "سال",
                    "Şammā" => "صما",
                    "Tibnah" => "تبنه",
                    "Umm Qays" => "أم قيس",
                    "Waqqāş" => "وقاص",
                    "Zaḩar" => "زحر",
                ],
            ],
            "Jerash Governorate" => [
                "ar" => "محافظة جرش",
                "cities" => [
                    "Al Kittah" => "الكته",
                    "Balīlā" => "بليلا",
                    "Burmā" => "برما",
                    "Jarash" => "جرش",
                    "Qafqafā" => "قففا",
                    "Raymūn" => "ريمون",
                    "Sakib" => "ساكب",
                    "Sūf" => "سوف",
                ],
            ],
            "Karak Governorate" => [
                "ar" => "محافظة الكرك",
                "cities" => [
                    "‘Ayy" => "عي",
                    "‘Izrā" => "إذراء",
                    "Adir" => "أدر",
                    "Al Khinzīrah" => "الخنزيرة",
                    "Al Mazār al Janūbī" => "المزار الجنوبي",
                    "Al Qaşr" => "القصر",
                    "Ar Rabbah" => "الربه",
                    "Karak City" => "مدينة الكرك",
                    "Safi" => "الصفا",
                ],
            ],
            "Ma'an Governorate" => [
                "ar" => "محافظة معان",
                "cities" => [
                    "Al Jafr" => "الجفر",
                    "Al Quwayrah" => "القويرة",
                    "Ash Shawbak" => "الشوبك",
                    "Aţ Ţayyibah" => "الطيبة",
                    "Ma'an" => "معان",
                    "Petra" => "البتراء",
                    "Qīr Moāv" => "قير مؤاب",
                ],
            ],
            "Madaba Governorate" => [
                "ar" => "محافظة مادبا",
                "cities" => [
                    "Mādabā" => "مادبا",
                ],
            ],
            "Mafraq Governorate" => [
                "ar" => "محافظة المفرق",
                "cities" => [
                    "Al Ḩamrā’" => "الحمرا",
                    "Mafraq" => "المفرق",
                    "Rehab" => "رحاب",
                    "Rukban" => "الركبان",
                    "Şabḩā" => "صبحا",
                    "Umm al Qiţţayn" => "أم القطين",
                ],
            ],
            "Tafilah Governorate" => [
                "ar" => "محافظة الطفيلة",
                "cities" => [
                    "Aţ Ţafīlah" => "الطفيلة",
                    "Buşayrā" => "بصيرا",
                ],
            ],
            "Zarqa Governorate" => [
                "ar" => "محافظة الزرقاء",
                "cities" => [
                    "Al Azraq ash Shamālī" => "الأزرق الشمالي",
                    "Russeifa" => "الرصيفة",
                    "Zarqa" => "الزرقاء",
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
