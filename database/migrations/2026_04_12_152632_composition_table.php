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
        Schema::create('composition', function (Blueprint $table) {
            $table->id('id_composition');
        
            $table->foreignId('grade_id')
                ->constrained('gradebeton', 'id_grade')
                ->cascadeOnDelete();
        
            $table->foreignId('inventory_id')
                ->constrained('inventory', 'id_inventory')
                ->cascadeOnDelete();
        
            $table->decimal('qty', 10, 2);
        
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
