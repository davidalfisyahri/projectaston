<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->decimal('stock_system', 12, 3)->change();
            $table->decimal('stock_actual', 12, 3)->change();
            $table->decimal('difference', 12, 3)->change();
        });
    }

    public function down(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->integer('stock_system')->change();
            $table->integer('stock_actual')->change();
            $table->integer('difference')->change();
        });
    }
};
