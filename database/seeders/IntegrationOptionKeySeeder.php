<?php

namespace Database\Seeders;

use App\Models\Integration;
use App\Models\IntegrationOptionKey;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IntegrationOptionKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add Hawa Chat Options Keys
        $hawaChat = Integration::where('name', 'hawa_chat')->first();
        if ($hawaChat) {
            IntegrationOptionKey::updateOrCreate([
                'integration_id' => $hawaChat->id,
                'key' => 'account_id',
                'parent_id' => null,
                'type' => 'option',
            ],
                [
                    'value' => 'hawaNo',
                ]);
        }

        // Add Sada Live Options Keys
        $sadaLive = Integration::where('name', 'sada_live')->first();
        if ($sadaLive) {
            IntegrationOptionKey::updateOrCreate([
                'integration_id' => $sadaLive->id,
                'key' => 'account_id',
                'parent_id' => null,
                'type' => 'option',
            ],
                [
                    'value' => 'to_uid',
                ]);
        }

        // Add Mint-route Options Keys
        $mintroute = Integration::where('name', 'mintroute')->first();
        if ($mintroute) {
            IntegrationOptionKey::updateOrCreate([
                'integration_id' => $mintroute->id,
                'key' => 'account_id',
                'parent_id' => null,
                'type' => 'option',
            ],
                [
                    'value' => 'account_id',
                ]);
        }
    }
}
