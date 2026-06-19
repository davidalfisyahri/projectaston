<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id('id_inventory');
            $table->string('name_material');
            $table->enum('type', [
                'cement',
                'FA',
                'Sand',
                'Aggregate',
                'Admixture',
                'Water'
            ]);
            $table->decimal('stock', 12, 3)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
