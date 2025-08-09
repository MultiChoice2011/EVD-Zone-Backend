<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Categoryseeder;
use App\Models\Vendor;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call(SaudiArabiaCountrySeeder::class);
        $this->call(RoleAndPermissionSeeder::class);
        $this->call(RoleAndPermissionSellerSeeder::class);
        $this->call(NotificationSettingSeeder::class);
        $this->call(OneCardSettingSeeder::class);
        $this->call(KuwaitCountrySeeder::class);
        $this->call(SettingDetailsSeeder::class);
        $this->call(StaticPageSeeder::class);
        $this->call(SuperAdminRoleSeeder::class);
        
    }
}
