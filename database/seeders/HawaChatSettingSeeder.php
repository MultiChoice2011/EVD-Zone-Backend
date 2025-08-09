<?php

namespace Database\Seeders;

use App\Models\Integration;
use App\Models\IntegrationKey;
use App\Services\General\OnlineShoppingIntegration\HawaChatService;
use Illuminate\Database\Seeder;

class HawaChatSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integration = Integration::updateOrCreate([
            'name' => 'hawa_chat',
            'model' => HawaChatService::class,
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
                'key' => 'merchant_uid',
                'value' => 'xxx',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'secret_key',
                'value' => 'xxx',
            ]
        ]);

    }
}
