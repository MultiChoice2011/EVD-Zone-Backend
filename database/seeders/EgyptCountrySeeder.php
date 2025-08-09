<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EgyptCountrySeeder extends Seeder
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
            'code' => 'EG',
            'flag' => 'eg.svg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Country Translations
        DB::table('country_translations')->insert([
            ['country_id' => $countryId, 'language_id' => $langArId, 'name' => 'مصر'],
            ['country_id' => $countryId, 'language_id' => $langEnId, 'name' => 'Egypt'],
        ]);

        // Regions and their respective cities
        $regions = [
            'Alexandria Governorate' => [
                'ar' => 'محافظة الإسكندرية',
                'cities' => [
                    'Alexandria' => 'الإسكندرية',
                ],
            ],
            'Aswan Governorate' => [
                'ar' => 'محافظة أسوان',
                'cities' => [
                    'Abu Simbel' => 'أبو سمبل',
                    'Aswan' => 'أسوان',
                    'Idfū' => 'إدفو',
                    'Kawm Umbū' => 'كوم أمبو',
                ],
            ],
            'Asyut Governorate' => [
                'ar' => 'محافظة أسيوط',
                'cities' => [
                    'Abnūb' => 'أبنوب',
                    'Abū Tīj' => 'أبو تيج',
                    'Al Badārī' => 'البداري',
                    'Al Qūşīyah' => 'القوصية',
                    'Asyūţ' => 'أسيوط',
                    'Dayrūţ' => 'ديروط',
                    'Manfalūţ' => 'منفلوط',
                ],
            ],
            'Beheira Governorate' => [
                'ar' => 'محافظة البحيرة',
                'cities' => [
                    'Abū al Maţāmīr' => 'أبو المطامير',
                    'Ad Dilinjāt' => 'الدلنجات',
                    'Damanhūr' => 'دمنهور',
                    'Ḩawsh ‘Īsá' => 'حوش عيسى',
                    'Idkū' => 'إدكو',
                    'Kafr ad Dawwār' => 'كفر الدوار',
                    'Kawm Ḩamādah' => 'كوم حمادة',
                    'Rosetta' => 'رشيد',
                ],
            ],
            'Beni Suef Governorate' => [
                'ar' => 'محافظة بني سويف',
                'cities' => [
                    'Al Fashn' => 'الفشن',
                    'Banī Suwayf' => 'بني سويف',
                    'Būsh' => 'بوش',
                    'Sumusţā as Sulţānī' => 'سمسطا السلطاني',
                ],
            ],
            'Cairo Governorate' => [
                'ar' => 'محافظة القاهرة',
                'cities' => [
                    'Cairo' => 'القاهرة',
                    'Ḩalwān' => 'حلوان',
                    'New Cairo' => 'القاهرة الجديدة',
                ],
            ],
            'Dakahlia Governorate' => [
                'ar' => 'محافظة الدقهلية',
                'cities' => [
                    '‘Izbat al Burj' => 'عزبة البرج',
                    'Ajā' => 'أجا',
                    'Al Jammālīyah' => 'الجمالية',
                    'Al Manşūrah' => 'المنصورة',
                    'Al Manzalah' => 'المنزلة',
                    'Al Maţarīyah' => 'المطرية',
                    'Bilqās' => 'بلقاس',
                    'Dikirnis' => 'دكرنس',
                    'Minyat an Naşr' => 'منية النصر',
                    'Shirbīn' => 'شربين',
                    'Ţalkhā' => 'طلخا',
                ],
            ],
            'Damietta Governorate' => [
                'ar' => 'محافظة دمياط',
                'cities' => [
                    'Az Zarqā' => 'الزرقاء',
                    'Damietta' => 'دمياط',
                    'Fāraskūr' => 'فارسكور',
                ],
            ],
            'Faiyum Governorate' => [
                'ar' => 'محافظة الفيوم',
                'cities' => [
                    'Al Fayyūm' => 'الفيوم',
                    'Al Wāsiţah' => 'الواسطى',
                    'Ibshawāy' => 'إبشواي',
                    'Iţsā' => 'إطسا',
                    'Ţāmiyah' => 'طامية',
                ],
            ],
            'Gharbia Governorate' => [
                'ar' => 'محافظة الغربية',
                'cities' => [
                    'Al Maḩallah al Kubrá' => 'المحلة الكبرى',
                    'Basyūn' => 'بسيون',
                    'Kafr az Zayyāt' => 'كفر الزيات',
                    'Quţūr' => 'قطور',
                    'Samannūd' => 'سمنود',
                    'Tanda' => 'طنطا',
                    'Zefta' => 'زفتى',
                ],
            ],
            'Giza Governorate' => [
                'ar' => 'محافظة الجيزة',
                'cities' => [
                    'Al ‘Ayyāţ' => 'العياط',
                    'Al Bawīţī' => 'البويطي',
                    'Al Ḩawāmidīyah' => 'الحوامدية',
                    'Aş Şaff' => 'الصف',
                    'Awsīm' => 'أوسيم',
                    'Giza' => 'الجيزة',
                    'Madīnat Sittah Uktūbar' => 'مدينة السادس من أكتوبر',
                ],
            ],
            'Ismailia Governorate' => [
                'ar' => 'محافظة الإسماعيلية',
                'cities' => [
                    'Ismailia' => 'الإسماعيلية',
                ],
            ],
            'Kafr el-Sheikh Governorate' => [
                'ar' => 'محافظة كفر الشيخ',
                'cities' => [
                    'Al Ḩāmūl' => 'الحامول',
                    'Disūq' => 'دسوق',
                    'Fuwwah' => 'فوه',
                    'Kafr ash Shaykh' => 'كفر الشيخ',
                    'Munshāt ‘Alī Āghā' => 'منشأة علي آغا',
                    'Sīdī Sālim' => 'سيدي سالم',
                ],
            ],
            'Luxor Governorate' => [
                'ar' => 'محافظة الأقصر',
                'cities' => [
                    'Luxor' => 'الأقصر',
                    'Markaz al Uqşur' => 'مركز الأقصر',
                ],
            ],
            'Matrouh Governorate' => [
                'ar' => 'محافظة مطروح',
                'cities' => [
                    'Al ‘Alamayn' => 'العلمين',
                    'Mersa Matruh' => 'مرسى مطروح',
                    'Siwa Oasis' => 'واحة سيوة',
                ],
            ],
            'Minya Governorate' => [
                'ar' => 'محافظة المنيا',
                'cities' => [
                    'Abū Qurqāş' => 'أبو قرقاص',
                    'Al Minyā' => 'المنيا',
                    'Banī Mazār' => 'بني مزار',
                    'Dayr Mawās' => 'دير مواس',
                    'Mallawī' => 'ملوي',
                    'Maţāy' => 'مطاي',
                    'Samālūţ' => 'سمالوط',
                ],
            ],
            'Monufia Governorate' => [
                'ar' => 'محافظة المنوفية',
                'cities' => [
                    'Al Bājūr' => 'الباجور',
                    'Ash Shuhadā’' => 'الشهداء',
                    'Ashmūn' => 'أشمون',
                    'Munūf' => 'منوف',
                    'Quwaysinā' => 'قويسنا',
                    'Shibīn al Kawm' => 'شبين الكوم',
                    'Talā' => 'تلا',
                ],
            ],
            'New Valley Governorate' => [
                'ar' => 'محافظة الوادي الجديد',
                'cities' => [
                    'Al Khārijah' => 'الخارجة',
                    'Qaşr al Farāfirah' => 'قصر الفرافرة',
                ],
            ],
            'North Sinai Governorate' => [
                'ar' => 'محافظة شمال سيناء',
                'cities' => [
                    'Arish' => 'العريش',
                ],
            ],
            'Port Said Governorate' => [
                'ar' => 'محافظة بورسعيد',
                'cities' => [
                    'Port Said' => 'بورسعيد',
                ],
            ],
            'Qalyubia Governorate' => [
                'ar' => 'محافظة القليوبية',
                'cities' => [
                    'Al Khānkah' => 'الخانكة',
                    'Al Qanāţir al Khayrīyah' => 'القناطر الخيرية',
                    'Banhā' => 'بنها',
                    'Qalyūb' => 'قليوب',
                    'Shibīn al Qanāṭir' => 'شبين القناطر',
                    'Toukh' => 'طوخ',
                ],
            ],
            'Qena Governorate' => [
                'ar' => 'محافظة قنا',
                'cities' => [
                    'Dishnā' => 'دشنا',
                    'Farshūţ' => 'فرشوط',
                    'Isnā' => 'إسنا',
                    'Kousa' => 'قوص',
                    'Naja\' Ḥammādī' => 'نجع حمادي',
                    'Qinā' => 'قنا',
                ],
            ],
            'Red Sea Governorate' => [
                'ar' => 'محافظة البحر الأحمر',
                'cities' => [
                    'Al Quşayr' => 'القصير',
                    'El Gouna' => 'الجونة',
                    'Hurghada' => 'الغردقة',
                    'Makadi Bay' => 'مكادي باي',
                    'Marsa Alam' => 'مرسى علم',
                    'Ras Gharib' => 'رأس غارب',
                    'Safaga' => 'سفاجا',
                ],
            ],
            'Sohag Governorate' => [
                'ar' => 'محافظة سوهاج',
                'cities' => [
                    'Akhmīm' => 'أخميم',
                    'Al Balyanā' => 'البلينا',
                    'Al Manshāh' => 'المنشاة',
                    'Jirjā' => 'جرجا',
                    'Juhaynah' => 'جهينة',
                    'Markaz Jirjā' => 'مركز جرجا',
                    'Markaz Sūhāj' => 'مركز سوهاج',
                    'Sohag' => 'سوهاج',
                    'Ţahţā' => 'طهطا',
                ],
            ],
            'South Sinai Governorate' => [
                'ar' => 'محافظة جنوب سيناء',
                'cities' => [
                    'Dahab' => 'دهب',
                    'El-Tor' => 'الطور',
                    'Nuwaybi‘a' => 'نويبع',
                    'Saint Catherine' => 'سانت كاترين',
                    'Sharm el-Sheikh' => 'شرم الشيخ',
                ],
            ],
            'Suez Governorate' => [
                'ar' => 'محافظة السويس',
                'cities' => [
                    'Ain Sukhna' => 'عين السخنة',
                    'Suez' => 'السويس',
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
