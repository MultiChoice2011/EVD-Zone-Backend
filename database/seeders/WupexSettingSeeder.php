<?php

namespace Database\Seeders;

use App\Models\Integration;
use App\Models\IntegrationKey;
use App\Models\Setting;
use App\Services\General\OnlineShoppingIntegration\OneCardService;
use App\Services\General\OnlineShoppingIntegration\WupexService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WupexSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integration = Integration::create([
            'name' => 'wupex',
            'model' => WupexService::class,
            'status' => 'active'
        ]);

        IntegrationKey::insert([
            [
                'integration_id' => $integration->id,
                'key' => 'url',
                'value' => 'https://sandbox-service.wupex.com',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'user_name',
                'value' => 'mail@ahmedhekal.com',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'secret_key',
                'value' => 'N4dSFs07QUI4BBZjb2yetCmusXEJDEPjx3NZ7jnvOBbWYTGozGUIBJGqEEbQdn5l0ST-BJg9XV7IYdgMgKcAigE1yvp00RmEaGo1-bNWX10QyKvAmhHr2gRsNVSe5ez2ixmW6dqrXkw',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'merchant_id',
                'value' => 'AL-BATAQAT001',
            ],
        ]);
    }
}
