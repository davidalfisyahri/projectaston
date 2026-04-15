<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('gradebeton')->insert([
            [
                'name_grade' => 'K-225',
                'mpa' => '18',
                'harga_fa' => 900000,
                'harga_nfa' => 950000,
            ],
            [
                'name_grade' => 'K-250',
                'mpa' => '20',
                'harga_fa' => 1000000,
                'harga_nfa' => 1050000,
            ],
        ]);
    }
}