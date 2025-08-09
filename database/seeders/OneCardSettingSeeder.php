<?php

namespace Database\Seeders;

use App\Models\Integration;
use App\Models\IntegrationKey;
use App\Models\Setting;
use App\Services\General\OnlineShoppingIntegration\OneCardService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OneCardSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integration = Integration::create([
            'name' => 'one_card',
            'model' => OneCardService::class,
            'status' => 'active'
        ]);

        IntegrationKey::insert([
            [
                'integration_id' => $integration->id,
                'key' => 'url',
                'value' => 'https://bbapi.ocstaging.net/integration/',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'user_name',
                'value' => 'mail@ahmedhekal.com',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'secret_key',
                'value' => 'Xk39dk3lmss#D',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'merchant_id',
                'value' => '',
            ],
        ]);
    }
}
