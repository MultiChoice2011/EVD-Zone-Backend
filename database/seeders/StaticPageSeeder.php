<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\StaticPage;
use App\Models\StaticPageTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StaticPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = Language::all();
        $staticPagesTranslatedArray = [
            'privacy_policy',
            'terms_conditions',
            'about_us',
        ];

        foreach ($staticPagesTranslatedArray as $staticPageTrans){
            $staticPage = StaticPage::create([
                'key' => $staticPageTrans,
                'web' => 1,
                'mobile' => 1,
            ]);
            foreach ($languages as $language) {
                $languageId = $language->id;
                StaticPageTranslation::create([
                    'static_page_id' => $staticPage->id,
                    'language_id' => $languageId,
                    'title' => 'test',
                    'meta_title' => 'test',
                    'meta_description' => 'test',
                    'meta_keywords' => 'test',
                    'content' => 'test',
                ]);
            }
        }
    }
}
