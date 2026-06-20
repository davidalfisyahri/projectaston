<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('gradebeton', function (Blueprint $table) {
            if (!Schema::hasColumn('gradebeton', 'harga_fa')) {
                $table->decimal('harga_fa', 12, 2)->default(0)->after('mpa');
            }
            if (!Schema::hasColumn('gradebeton', 'harga_nfa')) {
                $table->decimal('harga_nfa', 12, 2)->default(0)->after('harga_fa');
            }
        });

        // Copy existing data if 'harga' column exists
        if (Schema::hasColumn('gradebeton', 'harga')) {
            DB::table('gradebeton')->update([
                'harga_fa' => DB::raw('harga'),
                'harga_nfa' => DB::raw('harga'),
            ]);

            Schema::table('gradebeton', function (Blueprint $table) {
                $table->dropColumn('harga');
            });
        }
    }

    public function down(): void
    {
        Schema::table('gradebeton', function (Blueprint $table) {
            $table->decimal('harga', 12, 2)->default(0)->after('mpa');
        });

        DB::table('gradebeton')->update([
            'harga' => DB::raw('harga_fa'),
        ]);

        Schema::table('gradebeton', function (Blueprint $table) {
            $table->dropColumn(['harga_fa', 'harga_nfa']);
        });
    }
};
