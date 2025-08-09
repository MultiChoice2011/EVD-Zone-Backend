<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaudiArabiaCountrySeeder extends Seeder
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
            'code' => 'SA',
            'flag' => 'sa.svg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Country Translations
        DB::table('country_translations')->insert([
            ['country_id' => $countryId, 'language_id' => $langArId, 'name' => 'المملكة العربية السعودية'],
            ['country_id' => $countryId, 'language_id' => $langEnId, 'name' => 'Saudi Arabia'],
        ]);

        // Regions and their respective cities
        $regions = [
            'Asir Region' => [
                'ar' => 'منطقة عسير',
                'cities' => [
                    'Abha' => 'أبها',
                    'Al Majāridah' => 'المجاردة',
                    'An Nimāş' => 'النماص',
                    'Khamis Mushait' => 'خميس مشيط',
                    'Qal‘at Bīshah' => 'قلعة بيشة',
                    'Sabt Al Alayah' => 'سبت العلايا',
                    'Tabālah' => 'تبالة',
                ]
            ],
            'Al Bahah Region' => [
                'ar' => 'منطقة الباحة',
                'cities' => [
                    'Al Bahah' => 'الباحة',
                    'Al Mindak' => 'المندق',
                ]
            ],
            'Al Jawf Region' => [
                'ar' => 'منطقة الجوف',
                'cities' => [
                    'Qurayyat' => 'القريات',
                    'Sakakah' => 'سكاكا',
                    'Şuwayr' => 'صوير',
                    'Ţubarjal' => 'طبرجل',
                ]
            ],
            'Al Madinah Region' => [
                'ar' => 'منطقة المدينة المنورة',
                'cities' => [
                    'Al-`Ula' => 'العلا',
                    'Badr Ḩunayn' => 'بدر حنين',
                    'Medina' => 'المدينة المنورة',
                    'Sulţānah' => 'سلطانة',
                    'Yanbu' => 'ينبع',
                ]
            ],
            'Al-Qassim Region' => [
                'ar' => 'منطقة القصيم',
                'cities' => [
                    'Adh Dhibiyah' => 'الذيبية',
                    'Al Bukayrīyah' => 'البكيرية',
                    'Al Fuwayliq' => 'الفويلق',
                    'Al Mithnab' => 'المذنب',
                    'Ar Rass' => 'الرس',
                    'Buraydah' => 'بريدة',
                    'Tanūmah' => 'تنومة',
                    'Wed Alnkil' => 'وادي النكيل',
                ]
            ],
            'Eastern Province' => [
                'ar' => 'المنطقة الشرقية',
                'cities' => [
                    'Abqaiq' => 'بقيق',
                    'Al Awjām' => 'الأوجام',
                    'Al Baţţālīyah' => 'البطالية',
                    'Al Hufūf' => 'الهفوف',
                    'Al Jafr' => 'الجفر',
                    'Al Jubayl' => 'الجبيل',
                    'Al Khafjī' => 'الخفجي',
                    'Al Markaz' => 'المركز',
                    'Al Mubarraz' => 'المبرز',
                    'Al Munayzilah' => 'المنيزلة',
                    'Al Muţayrifī' => 'المطيرفي',
                    'Al Qārah' => 'القرى',
                    'Al Qaţīf' => 'القطيف',
                    'Al Qurayn' => 'القرين',
                    'As Saffānīyah' => 'السفانية',
                    'Aţ Ţaraf' => 'الطرف',
                    'At Tūbī' => 'التوبي',
                    'Dammam' => 'الدمام',
                    'Dhahran' => 'الظهران',
                    'Hafar Al-Batin' => 'حفر الباطن',
                    'Julayjilah' => 'جلجله',
                    'Khobar' => 'الخبر',
                    'Mulayjah' => 'مليجة',
                    'Qaisumah' => 'القيصومة',
                    'Raḩīmah' => 'رحيمة',
                    'Şafwá' => 'صفوى',
                    'Sayhāt' => 'سيهات',
                    'Tārūt' => 'تاروت',
                    'Umm as Sāhik' => 'أم الساهك',
                ]
            ],
            'Ha\'il Region' => [
                'ar' => 'منطقة حائل',
                'cities' => [
                    'Ha\'il' => 'حائل',
                ]
            ],
            'Jizan Region' => [
                'ar' => 'منطقة جازان',
                'cities' => [
                    'Abū ‘Arīsh' => 'أبو عريش',
                    'Ad Darb' => 'الدرب',
                    'Al Jarādīyah' => 'الجرادية',
                    'Farasān' => 'فرسان',
                    'Jizan' => 'جيزان',
                    'Mislīyah' => 'مسلية',
                    'Mizhirah' => 'مزهرة',
                    'Şabyā' => 'صبيا',
                    'Şāmitah' => 'صامطة',
                ]
            ],
            'Makkah Region' => [
                'ar' => 'منطقة مكة المكرمة',
                'cities' => [
                    'Al Hadā' => 'الهدا',
                    'Al Jumūm' => 'الجموم',
                    'Al Muwayh' => 'المويه',
                    'Ash Shafā' => 'الشفا',
                    'Ghran' => 'غران',
                    'Jeddah' => 'جدة',
                    'Mecca' => 'مكة',
                    'Rābigh' => 'رابغ',
                    'Ta’if' => 'الطائف',
                    'Turabah' => 'تربة',
                ]
            ],
            'Najran Region' => [
                'ar' => 'منطقة نجران',
                'cities' => [
                    'Najrān' => 'نجران',
                ]
            ],
            'Northern Borders Region' => [
                'ar' => 'منطقة الحدود الشمالية',
                'cities' => [
                    'Arar' => 'عرعر',
                    'Turaif' => 'طريف',
                ]
            ],
            'Riyadh Region' => [
                'ar' => 'منطقة الرياض',
                'cities' => [
                    'Ad Dawādimī' => 'الدودامي',
                    'Ad Dilam' => 'الدلم',
                    'Afif' => 'عفيف',
                    'Ain AlBaraha' => 'عين البرحة',
                    'Al Arţāwīyah' => 'الأرطاوية',
                    'Al Kharj' => 'الخرج',
                    'As Sulayyil' => 'السليل',
                    'Az Zulfī' => 'الزلفي',
                    'Marāt' => 'مرات',
                    'Riyadh' => 'الرياض',
                    'Sājir' => 'ساجر',
                    'shokhaibٍ' => 'شقية',
                    'Tumayr' => 'تمير',
                ]
            ],
            'Tabuk Region' => [
                'ar' => 'منطقة تبوك',
                'cities' => [
                    'Al Wajh' => 'الوجه',
                    'Duba' => 'ضباء',
                    'Tabuk' => 'تبوك',
                    'Umm Lajj' => 'أملج',
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
