<?php

namespace Database\Seeders;

use App\Models\Integration;
use App\Models\IntegrationKey;
use App\Services\General\OnlineShoppingIntegration\SadaLiveService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SadaLiveSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integration = Integration::updateOrCreate([
            'name' => 'sada_live',
            'model' => SadaLiveService::class,
        ],[
            'status' => 'active'
        ]);

        IntegrationKey::insert([
            [
                'integration_id' => $integration->id,
                'key' => 'url',
                'value' => '127.0.0.1:8000/',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'ga_uid',
                'value' => 'xxx',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'api_key',
                'value' => 'xxx',
            ]
        ]);
    }
}
