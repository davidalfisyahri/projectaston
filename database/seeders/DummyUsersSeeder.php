<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyUsersSeeder extends Seeder
{
    public function run(): void
    {
        $userData = [
            [
                'name_user' => 'Admin',
                'office_branch' => 'Jakarta',
                'nik' => '123456789',
                'role' => 'superadmin',
                'position' => 'direktur_utama',
                'username' => 'admin123',
                'password' => Hash::make('pass123'),
            ]
        ];

        foreach ($userData as $val) {
            User::create($val);
        }
    }
}