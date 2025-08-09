<?php

namespace Database\Seeders;

use App\Models\Integration;
use App\Models\IntegrationKey;
use App\Models\IntegrationOptionKey;
use App\Services\General\OnlineShoppingIntegration\SadaLiveService;
use Illuminate\Database\Seeder;

class QuickStoreSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integration = Integration::create([
            'name' => 'quick_store',
            'model' => SadaLiveService::class,
            'status' => 'active'
        ]);

        IntegrationKey::insert([
            [
                'integration_id' => $integration->id,
                'key' => 'url',
                'value' => 'https://api.quick4store.com/client/api/',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'api_token',
                'value' => 'dddddddddddd',
            ]
        ]);

        IntegrationOptionKey::insert([
            [
                'integration_id' => $integration->id,
                'key' => 'account_id',
                'type' => 'option',
                'value' => 'user_id',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'phone',
                'type' => 'option',
                'value' => 'phone',
            ]
        ]);
    }
}
