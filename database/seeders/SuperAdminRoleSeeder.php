<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Permission;
use App\Models\PermissionTranslation;
use App\Models\Role;
use App\Models\RoleTranslation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

//use Spatie\Permission\Models\Permission;
//use Spatie\Permission\Models\Role;

class SuperAdminRoleSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // assign super admin Role for first admin
        $languages = Language::all();

        $rolesTranslations = [
            'ar' => 'ادمن عام',
            'en' => 'super admin',
        ];

        // add roles with its translations
        $adminRole = Role::create(['guard_name' => 'adminApi', 'name' => 'Super Admin']);
        foreach ($languages as $language) {
            RoleTranslation::create([
                'role_id' => $adminRole->id,
                'language_id' => $language->id,
                'display_name' => $rolesTranslations[$language->code],
            ]);
        }

        $admin = User::first();
        $admin->assignRole('Super Admin');


    }
}
