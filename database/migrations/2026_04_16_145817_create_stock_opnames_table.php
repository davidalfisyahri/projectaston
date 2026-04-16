<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->integer('stock_system');
            $table->integer('stock_actual');
            $table->integer('difference');
            $table->text('notes')->nullable();
            $table->date('opname_date');
            $table->unsignedBigInteger('checked_by');
            $table->timestamps();

            $table->foreign('inventory_id')->references('id_inventory')->on('inventory')->onDelete('cascade');
            $table->foreign('checked_by')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
