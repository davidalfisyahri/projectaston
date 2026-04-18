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
        Schema::create('customer_request_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_request_id');
            $table->unsignedBigInteger('grade_id');
        
            $table->enum('type', ['fa','nfa']);
            $table->decimal('qty', 10,2);
        
            $table->decimal('price', 12,2); // harga satuan
            $table->decimal('total', 12,2); // qty * price
        
            $table->timestamps();
        
            $table->foreign('customer_request_id')
                ->references('id')->on('customer_requests')
                ->onDelete('cascade');
        
            $table->foreign('grade_id')
                ->references('id_grade')->on('gradebeton')
                ->onDelete('cascade');
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
