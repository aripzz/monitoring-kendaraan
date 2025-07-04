<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UsersParent;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('123123'),
            'role' => 'admin',
        ]);

        // Create Supervisor User
        $supervisor = User::create([
            'name' => 'Supervisor Utama',
            'email' => 'supervisor@example.com',
            'password' => Hash::make('123123'),
            'role' => 'supervisor',
        ]);

        // Create Manager User
        $manager = User::create([
            'name' => 'Manager Operasional',
            'email' => 'manager@example.com',
            'password' => Hash::make('123123'),
            'role' => 'manager',
        ]);

        // Create Employee User
        $employee = User::create([
            'name' => 'Karyawan Staff',
            'email' => 'employee@example.com',
            'password' => Hash::make('123123'),
            'role' => 'employee',
        ]);

        // Create Driver 1
        $driver1 = User::create([
            'name' => 'Driver Pertama',
            'email' => 'driver1@example.com',
            'password' => Hash::make('123123'),
            'role' => 'driver',
        ]);

        // Create Driver 2
        $driver2 = User::create([
            'name' => 'Driver Kedua',
            'email' => 'driver2@example.com',
            'password' => Hash::make('123123'),
            'role' => 'driver',
        ]);

        echo "6 users created successfully with hierarchy:\n";
        echo "- Admin: admin@example.com\n";
        echo "- Supervisor: supervisor@example.com\n";
        echo "- Manager: manager@example.com\n";
        echo "- Employee: employee@example.com\n";
        echo "- Driver 1: driver1@example.com\n";
        echo "- Driver 2: driver2@example.com\n";
        echo "All passwords: 123123\n";
    }
}