<?php

namespace Database\Seeders;

use App\Models\Integration;
use App\Models\IntegrationKey;
use App\Models\Setting;
use App\Services\General\SmsVerification\MsegatService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MsegatSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integration = Integration::create([
                'name' => 'msegat',
                'model' => MsegatService::class,
                'status' => 'active'
        ]);

        IntegrationKey::insert([
            [
                'integration_id' => $integration->id,
                'key' => 'url',
                'value' => 'https://www.msegat.com/gw/',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'username',
                'value' => 'ATC@2030',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'api_key',
                'value' => '4ef65f082cf633a6a0342c118bc14abd',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'user_sender',
                'value' => 'ATC',
            ],
        ]);
    }
}
