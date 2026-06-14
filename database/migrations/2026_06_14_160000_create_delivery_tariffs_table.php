<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_tariffs', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_km', 8, 2);       // batas bawah jarak
            $table->decimal('max_km', 8, 2);        // batas atas jarak
            $table->decimal('fee', 15, 2);           // biaya pengiriman
            $table->string('label')->nullable();     // label tampilan, e.g. "10 - 15 km"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_tariffs');
    }
};
