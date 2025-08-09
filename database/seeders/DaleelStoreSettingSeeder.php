<?php

namespace Database\Seeders;

use App\Models\Integration;
use App\Models\IntegrationKey;
use App\Models\Setting;
use App\Services\General\OnlineShoppingIntegration\DaleelStoreService;
use App\Services\General\OnlineShoppingIntegration\OneCardService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DaleelStoreSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integration = Integration::create([
            'name' => 'daleel_store',
            'model' => DaleelStoreService::class,
            'status' => 'active'
        ]);

        IntegrationKey::insert([
            [
                'integration_id' => $integration->id,
                'key' => 'url',
                'value' => 'https://daleelapi.com/api/v1/',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'username',
                'value' => 'merchants.api@daleelstore.com',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'password',
                'value' => 'ABC12345',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'client_id',
                'value' => '2',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'client_secret',
                'value' => 'D1uvg3aUd79fDIvqduYlaV3q1b59XztrPPooADyDrIo=',
            ],
        ]);
    }
}
