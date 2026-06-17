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
        Schema::table('composition', function (Blueprint $table) {
            $table->string('recipe_type')->nullable()->after('inventory_id'); // e.g. 'FA' or 'NFA'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('composition', function (Blueprint $table) {
            $table->dropColumn('recipe_type');
        });
    }
};
