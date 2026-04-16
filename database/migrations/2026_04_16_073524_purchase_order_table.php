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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id('id_po');
            $table->string('no_po');
            $table->unsignedBigInteger('supplier_id');
            $table->foreign('supplier_id')
                ->references('id_supplier')
                ->on('suppliers')
                ->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('created_by');
            $table->decimal('total', 15, 2)->default(0);
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
