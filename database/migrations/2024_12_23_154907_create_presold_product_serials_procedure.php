<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('
            CREATE PROCEDURE update_product_serials(IN productId BIGINT, IN quantity INT)
            BEGIN
                -- Update product_serials
                UPDATE `product_serials`
                SET `status` = "presold"
                WHERE `product_id` = productId
                  AND `expiring` >= NOW()
                  AND `status` = "free"
                LIMIT quantity;

            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('update_product_serials');
    }
};
