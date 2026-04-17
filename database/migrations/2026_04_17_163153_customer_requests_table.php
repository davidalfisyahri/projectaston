<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_requests', function (Blueprint $table) {
            $table->id();

            $table->string('request_code')->unique()->nullable();

            // ✅ FIX FOREIGN KEY (WAJIB)
            $table->unsignedBigInteger('created_by');

            $table->date('tanggal');
            $table->string('region')->nullable();
            $table->string('customer_number')->nullable();

            $table->string('customer_name');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();

            $table->text('note')->nullable();

            $table->enum('status', [
                'draft',
                'waiting_approval',
                'approved',
                'rejected',
                'paid',
                'confirmed_wa',
                'scheduled',
                'done'
            ])->default('draft');

            $table->boolean('is_paid')->default(false);
            $table->boolean('is_wa_confirmed')->default(false);

            $table->date('schedule_date')->nullable();

            $table->timestamps();

            // ✅ INDEX
            $table->index('status');
            $table->index('created_by');

            // ✅ 🔥 INI YANG PENTING (FK KE id_user)
            $table->foreign('created_by')
                  ->references('id_user')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_requests');
    }
};