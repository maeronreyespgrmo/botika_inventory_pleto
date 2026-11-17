<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['full_name' => 'System Administrator', 'username' => 'admin', 'role' => 'admin'],
            ['full_name' => 'Demo Pharmacist', 'username' => 'pharmacist', 'role' => 'pharmacist'],
            ['full_name' => 'Demo Cashier', 'username' => 'cashier', 'role' => 'cashier'],
        ] as $user) {
            User::firstOrCreate(
                ['username' => $user['username']],
                $user + ['password' => 'password'],
            );
        }
    }
}
