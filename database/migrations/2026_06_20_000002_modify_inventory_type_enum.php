<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->enum('type', [
                'cement',
                'FA',
                'NFA',
                'Sand',
                'Aggregate',
                'Admixture',
                'Water',
                'Slump'
            ])->change();
        });
    }

    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->enum('type', [
                'cement',
                'FA',
                'Sand',
                'Aggregate',
                'Admixture',
                'Water'
            ])->change();
        });
    }
};
