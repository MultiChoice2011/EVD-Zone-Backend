<?php

namespace Database\Seeders;

use App\Models\BankCommission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankCommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ar = DB::table('languages')->where('code', 'ar')->first();
        $en = DB::table('languages')->where('code', 'en')->first();

        $firstRecord =  BankCommission::create([
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('bank_commission_translations')->insert([
            'bank_commission_id' => $firstRecord->id,
            'language_id' => $ar->id,
            'name' => 'هايبر باى',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('bank_commission_translations')->insert([
            'bank_commission_id' => $firstRecord->id,
            'language_id' => $en->id,
            'name' => 'hyperpay',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
