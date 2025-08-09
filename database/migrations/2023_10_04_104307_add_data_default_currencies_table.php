<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        if (Schema::hasTable('currencies')) {
            $Currency = \App\Models\Currency::where('id',1)->first();
            // Insert $Currency stuff
            if (!$Currency) {
                DB::table('currencies')->insert(
                    array(
                        'id' => 1,
                        'name' => 'الدولار',
                        'code' => 'USD',
                        'is_default' => 1,
                        'value' => 1
                    )
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
