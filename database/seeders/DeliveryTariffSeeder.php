<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeliveryTariff;

class DeliveryTariffSeeder extends Seeder
{
    public function run(): void
    {
        $tariffs = [
            ['min_km' => 0,  'max_km' => 10, 'fee' => 0,       'label' => '0 - 10 km (Gratis)'],
            ['min_km' => 10, 'max_km' => 15, 'fee' => 1000000, 'label' => '10 - 15 km'],
            ['min_km' => 15, 'max_km' => 25, 'fee' => 1200000, 'label' => '15 - 25 km'],
            ['min_km' => 25, 'max_km' => 40, 'fee' => 1500000, 'label' => '25 - 40 km'],
        ];

        foreach ($tariffs as $tariff) {
            DeliveryTariff::updateOrCreate(
                ['min_km' => $tariff['min_km'], 'max_km' => $tariff['max_km']],
                $tariff
            );
        }
    }
}
