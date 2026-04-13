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
        Schema::create('gradebeton', function (Blueprint $table) {
            $table->id('id_grade');
            $table->string('name_grade'); // K-250
            $table->string('mpa');  // 20, 25
            $table->decimal('harga_fa', 12, 2)->default(0);
            $table->decimal('harga_nfa', 12, 2)->default(0);
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
