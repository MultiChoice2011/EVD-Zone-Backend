<?php

namespace Database\Seeders;

use App\Enums\VendorStatus;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ServiceVendorSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vendor::create([
            'name' => 'special-service-vendor',
            'owner_name' => 'special-service-vendor',
            'status' => VendorStatus::APPROVED,
            'phone' => '055555',
            'email' => 'MC@mail.com',
        ]);

    }

}
