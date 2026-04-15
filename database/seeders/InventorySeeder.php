<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('inventory')->insert([
            ['name_material' => 'Semen Tiga Roda', 'type' => 'cement', 'stock' => 1000],
            ['name_material' => 'Fly Ash Surabaya', 'type' => 'FA', 'stock' => 500],
            ['name_material' => 'Pasir Lumajang', 'type' => 'Sand', 'stock' => 2000],
            ['name_material' => 'Batu Split 1-2', 'type' => 'Aggregate', 'stock' => 3000],
            ['name_material' => 'Admixture Type F', 'type' => 'Admixture', 'stock' => 200],
        ]);
    }
}