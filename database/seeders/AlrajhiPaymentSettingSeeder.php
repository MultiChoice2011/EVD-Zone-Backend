<?php

namespace Database\Seeders;

use App\Models\Integration;
use App\Models\IntegrationKey;
use App\Models\Setting;
use App\Services\General\PaymentGateways\AlrajhiPaymentService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlrajhiPaymentSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integration = Integration::create([
            'name' => 'alrajhi_payment',
            'model' => AlrajhiPaymentService::class,
            'status' => 'active'
        ]);

        IntegrationKey::insert([
            [
                'integration_id' => $integration->id,
                'key' => 'url',
                'value' => 'https://securepayments.alrajhibank.com.sa/pg/payment/',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'tranportal_id',
                'value' => '2dhVM1YyGp950rW',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'tranportal_password',
                'value' => 'ezU9NR70Xc@8#z@',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'resource_key',
                'value' => '41260973939541260973939541260973',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'currency_code',
                'value' => '682',
            ],
        ]);
    }
}
