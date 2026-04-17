<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_request_approvals', function (Blueprint $table) {
            $table->id();

            // ✅ FK ke customer_requests
            $table->unsignedBigInteger('customer_request_id');

            // ✅ SAMAKAN DENGAN users.position
            $table->enum('role', [
                'wakil_direktur',
                'direktur_utama'
            ]);

            // ✅ FK users
            $table->unsignedBigInteger('approved_by')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            // ✅ RELASI
            $table->foreign('customer_request_id')
                  ->references('id')
                  ->on('customer_requests')
                  ->onDelete('cascade');

            $table->foreign('approved_by')
                  ->references('id_user')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_request_approvals');
    }
};