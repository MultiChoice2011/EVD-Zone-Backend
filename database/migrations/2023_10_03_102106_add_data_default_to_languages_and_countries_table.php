<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('languages')) {
            // Insert some stuff
            $lang = \App\Models\Language::where('id',1)->first();
            if (!$lang) {
                DB::table('languages')->insert(
                    array(
                        'id' => 1,
                        'name' => 'العربية',
                        'code' => 'ar'
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

    }
};
