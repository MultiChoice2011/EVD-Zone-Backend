<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IraqCountrySeeder extends Seeder
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
            'code' => 'IQ',
            'flag' => 'iq.svg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Country Translations
        DB::table('country_translations')->insert([
            ['country_id' => $countryId, 'language_id' => $langArId, 'name' => 'العراق'],
            ['country_id' => $countryId, 'language_id' => $langEnId, 'name' => 'Iraq'],
        ]);

        // Regions and their respective cities
        $regions = [
            'Al Anbar Governorate' => [
                'ar' => 'محافظة الأنبار',
                'cities' => [
                    '‘Anah' => 'عانة',
                    '‘Anat al Qadīmah' => 'عانة القديمة',
                    'Al Fallūjah' => 'الفلوجة',
                    'Ar Ruţbah' => 'الرطبة',
                    'Ḩadīthah' => 'حديثة',
                    'Hīt' => 'هيت',
                    'Hīt District' => 'قضاء هيت',
                    'Ramadi' => 'الرمادي',
                    'Rāwah' => 'راوة',
                ],
            ],
            'Al Muthanna Governorate' => [
                'ar' => 'محافظة المثنى',
                'cities' => [
                    'Ar Rumaythah' => 'الرميثة',
                    'As Samawah' => 'السماوة',
                ],
            ],
            'Al-Qādisiyyah Governorate' => [
                'ar' => 'محافظة القادسية',
                'cities' => [
                    '‘Afak' => 'عفك',
                    'Ad Dīwānīyah' => 'الديوانية',
                    'Ash Shāmīyah' => 'الشامية',
                    'Nāḩiyat ash Shināfīyah' => 'ناحية الشنافية',
                    'Nahiyat Ghammas' => 'ناحية غماس',
                ],
            ],
            'Babylon Governorate' => [
                'ar' => 'محافظة بابل',
                'cities' => [
                    'Al Ḩillah' => 'الحلة',
                    'Al Musayyib' => 'المسيب',
                    'Imam Qasim' => 'الإمام قاسم',
                    'Nāḩīyat Saddat al Hindīyah' => 'ناحية سدّة الهندية',
                ],
            ],
            'Baghdad Governorate' => [
                'ar' => 'محافظة بغداد',
                'cities' => [
                    'Abu Ghraib District' => 'قضاء أبو غريب',
                    'Abū Ghurayb' => 'أبو غريب',
                    'Baghdad' => 'بغداد',
                ],
            ],
            'Basra Governorate' => [
                'ar' => 'محافظة البصرة',
                'cities' => [
                    'Al Başrah al Qadīmah' => 'البصرة القديمة',
                    'Al Fāw' => 'الفاو',
                    'Al Hārithah' => 'الهارثة',
                    'Az Zubayr' => 'الزبير',
                    'Basrah' => 'البصرة',
                    'Umm Qaşr' => 'أم قصر',
                ],
            ],
            'Dhi Qar Governorate' => [
                'ar' => 'محافظة ذي قار',
                'cities' => [
                    'Ash Shaţrah' => 'الشطرة',
                    'Nāḩiyat al Fuhūd' => 'ناحية الفهود',
                    'Nasiriyah' => 'الناصرية',
                ],
            ],
            'Diyala Governorate' => [
                'ar' => 'محافظة ديالى',
                'cities' => [
                    'Al Miqdādīyah' => 'المقدادية',
                    'Baladrūz' => 'بلدروز',
                    'Baqubah' => 'بعقوبة',
                    'Khāliş' => 'خالص',
                    'Kifrī' => 'كفري',
                    'Mandalī' => 'مندلي',
                    'Qaḑā’ Kifrī' => 'قضاء كفري',
                ],
            ],
            'Dohuk Governorate' => [
                'ar' => 'محافظة دهوك',
                'cities' => [
                    'Al ‘Amādīyah' => 'العمادية',
                    'Batifa' => 'باطوفة',
                    'Dihok' => 'دهوك',
                    'Sīnah' => 'سينا',
                    'Zaxo' => 'زاخو',
                ],
            ],
            'Erbil Governorate' => [
                'ar' => 'محافظة أربيل',
                'cities' => [
                    'Arbil' => 'أربيل',
                    'Erbil' => 'إربيل',
                    'Koysinceq' => 'كويسنجق',
                    'Ruwāndiz' => 'رواندز',
                    'Shaqlāwah' => 'شقلاوة',
                    'Soran' => 'سوران',
                ],
            ],
            'Karbala Governorate' => [
                'ar' => 'محافظة كربلاء',
                'cities' => [
                    'Al Hindīyah' => 'الهندية',
                    'Karbala' => 'كربلاء',
                ],
            ],
            'Kirkuk Governorate' => [
                'ar' => 'محافظة كركوك',
                'cities' => [
                    'Kirkuk' => 'كركوك',
                ],
            ],
            'Maysan Governorate' => [
                'ar' => 'محافظة ميسان',
                'cities' => [
                    '‘Alī al Gharbī' => 'علي الغربي',
                    'Al ‘Amārah' => 'العمارة',
                    'Al-Mejar Al-Kabi District' => 'قضاء المجر الكبير',
                ],
            ],
            'Najaf Governorate' => [
                'ar' => 'محافظة النجف',
                'cities' => [
                    'Al Mishkhāb' => 'المشخاب',
                    'Kufa' => 'الكوفة',
                    'Najaf' => 'النجف',
                ],
            ],
            'Nineveh Governorate' => [
                'ar' => 'محافظة نينوى',
                'cities' => [
                    '‘Aqrah' => 'عقرة',
                    'Al Mawşil al Jadīdah' => 'الموصل الجديدة',
                    'Al-Hamdaniya' => 'الحمدانية',
                    'Ash Shaykhān' => 'الشيخان',
                    'Mosul' => 'الموصل',
                    'Sinjar' => 'سنجار',
                    'Sinjār' => 'سنجار',
                    'Tall ‘Afar' => 'تلعفر',
                    'Tallkayf' => 'تلكيف',
                ],
            ],
            'Saladin Governorate' => [
                'ar' => 'محافظة صلاح الدين',
                'cities' => [
                    'Ad Dujayl' => 'الدجيل',
                    'Balad' => 'بلد',
                    'Bayjī' => 'بيجي',
                    'Sāmarrā’' => 'سامراء',
                    'Tikrīt' => 'تكريت',
                    'Tozkhurmato' => 'طوزخورماتو',
                ],
            ],
            'Sulaymaniyah Governorate' => [
                'ar' => 'محافظة السليمانية',
                'cities' => [
                    'As Sulaymānīyah' => 'السليمانية',
                    'Baynjiwayn' => 'بنجيان',
                    'Ḩalabjah' => 'حلبجة',
                    'Jamjamāl' => 'جمجمال',
                ],
            ],
            'Wasit Governorate' => [
                'ar' => 'محافظة واسط',
                'cities' => [
                    'Al ‘Azīzīyah' => 'العزيزية',
                    'Al Ḩayy' => 'الحي',
                    'Al Kūt' => 'الكوت',
                    'Aş Şuwayrah' => 'الصويرة',
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
