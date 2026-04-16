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
        Schema::create('purchase_order_details', function (Blueprint $table) {
            $table->id('id_detail');
        
            $table->foreignId('po_id')
                ->constrained('purchase_orders', 'id_po')
                ->cascadeOnDelete();
        
            $table->string('item_name');
            $table->string('unit');
            $table->decimal('qty', 12, 2);
            $table->decimal('price', 12, 2);
            $table->decimal('total', 15, 2);
        
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
