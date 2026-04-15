<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompositionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('composition')->insert([

            // K-225
            ['grade_id' => 1, 'inventory_id' => 1, 'qty' => 300], // semen
            ['grade_id' => 1, 'inventory_id' => 2, 'qty' => 100], // FA
            ['grade_id' => 1, 'inventory_id' => 3, 'qty' => 800], // pasir
            ['grade_id' => 1, 'inventory_id' => 4, 'qty' => 1000], // batu

            // K-250
            ['grade_id' => 2, 'inventory_id' => 1, 'qty' => 350],
            ['grade_id' => 2, 'inventory_id' => 2, 'qty' => 120],
            ['grade_id' => 2, 'inventory_id' => 3, 'qty' => 750],
            ['grade_id' => 2, 'inventory_id' => 4, 'qty' => 1100],

        ]);
    }
}