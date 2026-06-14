<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_requests', function (Blueprint $table) {
            $table->decimal('delivery_distance', 8, 2)->nullable()->after('schedule_date');  // jarak km
            $table->decimal('delivery_fee', 15, 2)->default(0)->after('delivery_distance');   // biaya pengiriman
            $table->decimal('grand_total', 15, 2)->default(0)->after('delivery_fee');          // total keseluruhan
        });
    }

    public function down(): void
    {
        Schema::table('customer_requests', function (Blueprint $table) {
            $table->dropColumn(['delivery_distance', 'delivery_fee', 'grand_total']);
        });
    }
};
