<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Role;
use App\Models\RoleTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperSellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // assign super admin Role for first admin
        $languages = Language::all();

        $rolesTranslations = [
            'ar' => 'تاجر عام',
            'en' => 'super seller',
        ];

        // add roles with its translations
        $sellerRole = Role::create(['guard_name' => 'sellerApi', 'name' => 'Super Seller']);
        foreach ($languages as $language) {
            RoleTranslation::create([
                'role_id' => $sellerRole->id,
                'language_id' => $language->id,
                'display_name' => $rolesTranslations[$language->code],
            ]);
        }
    }
}
