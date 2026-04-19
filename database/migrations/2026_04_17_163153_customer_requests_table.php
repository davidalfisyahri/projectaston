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

            $table->unsignedBigInteger('created_by');

            // =====================
            // IDENTITAS
            // =====================
            $table->date('tanggal');
            $table->string('region')->nullable();
            $table->string('customer_number')->nullable();

            $table->string('customer_name');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->text('note')->nullable();

            // =====================
            // PROFIL BISNIS
            // =====================
            $table->string('no_identitas')->nullable();
            $table->string('form_business')->nullable();
            $table->string('section_business')->nullable();
            $table->text('address_business')->nullable();

            // =====================
            // PAJAK
            // =====================
            $table->string('npwp')->nullable();
            $table->string('tax_name')->nullable();
            $table->text('tax_address')->nullable();

            // =====================
            // PERIZINAN
            // =====================
            $table->string('izin_tdp')->nullable();
            $table->date('tdp_date')->nullable();

            $table->string('izin_siup')->nullable();
            $table->date('siup_date')->nullable();

            $table->string('izin_sio')->nullable();
            $table->date('sio_date')->nullable();

            // =====================
            // OWNER
            // =====================
            $table->string('owner_name')->nullable();
            $table->text('owner_address')->nullable();
            $table->string('email')->nullable();
            $table->string('business_ownership')->nullable();

            // =====================
            // PROJECT
            // =====================
            $table->text('office_address')->nullable();
            $table->text('ongoing_project')->nullable();

            // =====================
            // STATUS
            // =====================
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

            // INDEX
            $table->index('status');
            $table->index('created_by');

            // FOREIGN KEY
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