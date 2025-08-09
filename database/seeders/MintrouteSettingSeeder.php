<?php

namespace Database\Seeders;

use App\Models\Integration;
use App\Models\IntegrationKey;
use App\Models\IntegrationOptionKey;
use App\Services\General\OnlineShoppingIntegration\MintrouteTopupService;
use App\Services\General\OnlineShoppingIntegration\MintrouteVoucherService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MintrouteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integration = Integration::create([
            'name' => 'mintroute',
            'model' => MintrouteVoucherService::class,
            'status' => 'active'
        ]);

        IntegrationKey::insert([
            [
                'integration_id' => $integration->id,
                'key' => 'url_voucher',
                'value' => 'https://sandbox.mintroute.com/vendor/api/',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'url_topup',
                'value' => 'https://sandbox.mintroute.com/top_up/api/',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'access_key',
                'value' => '0lBcviPE',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'private_key',
                'value' => '22a5200e29483dac8e2bf3b8d399330d',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'username',
                'value' => 'nawaf.single',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'password',
                'value' => 'pgy14SsF',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'currency',
                'value' => 'USD',
            ]
        ]);

        IntegrationOptionKey::insert([
            [
                'integration_id' => $integration->id,
                'key' => 'account_id',
                'type' => 'option',
                'value' => 'account_id',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'zone_id',
                'type' => 'option',
                'value' => 'zone_id',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'server_id',
                'type' => 'option',
                'value' => 'server_id',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'server_id',
                'type' => 'option_value',
                'value' => '02',
            ]
        ]);

    }
}
