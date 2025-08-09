<?php

namespace Database\Seeders;

use App\Models\Integration;
use App\Models\IntegrationKey;
use App\Models\Setting;
use App\Services\General\OnlineShoppingIntegration\LikeCardService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LikeCardSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integration = Integration::create([
            'name' => 'like_card',
            'model' => LikeCardService::class,
            'status' => 'active'
        ]);

        IntegrationKey::insert([
            [
                'integration_id' => $integration->id,
                'key' => 'url',
                'value' => 'https://taxes.like4app.com/',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'device_id',
                'value' => '5d5799c6dc942a903c0d467150baa933804bf83a068724f588f9aa6ea6a6eca6',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'email',
                'value' => 'mail@ahmedhekal.com',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'hash_key',
                'value' => '8Tyr4EDw!2sN',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'phone',
                'value' => '201000602035',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'Secret_iv',
                'value' => 'St@cE4eZ',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'password',
                'value' => 'Pass@100100##',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'secret_key',
                'value' => 't-3zafRa',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'security_code',
                'value' => 'e544bac5b2735b0673a1038d84c0209d7e15d5cc7edecd14f897fe31edda09e2',
            ],
        ]);
    }
}
