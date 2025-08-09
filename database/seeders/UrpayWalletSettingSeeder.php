<?php

namespace Database\Seeders;

use App\Models\Integration;
use App\Models\IntegrationKey;
use App\Models\Setting;
use App\Services\General\UrpayWallet\UrpayWalletService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UrpayWalletSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integration = Integration::create([
            'name' => 'urpay',
            'model' => UrpayWalletService::class,
            'status' => 'active'
        ]);

        IntegrationKey::insert([
            [
                'integration_id' => $integration->id,
                'key' => 'url',
                'value' => 'https://walletsit.neoleap.com.sa/merchantb2b/v1/',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'merchant_id',
                'value' => '20293',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'wallet_number',
                'value' => '18386',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'terminal_id',
                'value' => '3997',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'client_id',
                'value' => '1278490599',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'username',
                'value' => '5274dcf8-0dd9-11ee-963f-c0a864430000',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'password',
                'value' => '805ry0gnZD3xjOxE6EuHuErBsf3quhAf',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'consumer_mobile_number',
                'value' => '+966568595106',
            ],
        ]);
    }
}
