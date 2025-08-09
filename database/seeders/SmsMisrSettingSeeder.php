<?php

namespace Database\Seeders;

use App\Models\Integration;
use App\Models\IntegrationKey;
use App\Models\Setting;
use App\Services\General\SmsVerification\SmsMisrService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SmsMisrSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integration = Integration::create([
            'name' => 'smsmisr',
            'model' => SmsMisrService::class,
            'status' => 'active'
        ]);

        IntegrationKey::insert([
            [
                'integration_id' => $integration->id,
                'key' => 'url',
                'value' => 'https://smsmisr.com/api/',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'environment',
                'value' => '2',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'username',
                'value' => '59194fd6-5f30-412b-84a9-fae8ebd39e04',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'password',
                'value' => 'f687dacd7c7ee7c012d4eaaad70b705b1356b8123de7bcc1dc052c41546cc465',
            ],
            [
                'integration_id' => $integration->id,
                'key' => 'sender_id',
                'value' => 'b611afb996655a94c8e942a823f1421de42bf8335d24ba1f84c437b2ab11ca27',
            ],
        ]);
    }
}
