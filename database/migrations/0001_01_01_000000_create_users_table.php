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
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('name_user');
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('office_branch')->nullable();
            $table->string('nik')->nullable();
            $table->enum('role', ['superadmin','admin','sales']);
            $table->enum('position', [
                'sales_internal',
                'sales_external',
                'wakil_direktur',
                'direktur_utama',
                'hrga',
                'logistik',
                'finance'
            ]);
            $table->string('username');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
