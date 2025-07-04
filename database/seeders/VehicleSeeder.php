<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vehicles;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 vehicles for goods transport (pengangkut_barang)
        $goodsVehicles = [
            [
                'plate_number' => 'B 1234 ABC',
                'model' => 'Mitsubishi Colt Diesel',
                'type' => 'pengangkut_barang',
                'owner' => 'inhouse',
                'bbm' => 8.5,
                'next_service_date' => '2024-12-15',
            ],
            [
                'plate_number' => 'B 5678 DEF',
                'model' => 'Isuzu Elf',
                'type' => 'pengangkut_barang',
                'owner' => 'rental',
                'bbm' => 7.2,
                'next_service_date' => '2024-11-20',
            ],
            [
                'plate_number' => 'B 9012 GHI',
                'model' => 'Hino Dutro',
                'type' => 'pengangkut_barang',
                'owner' => 'inhouse',
                'bbm' => 6.8,
                'next_service_date' => '2024-12-30',
            ],
            [
                'plate_number' => 'B 3456 JKL',
                'model' => 'Daihatsu Gran Max Pick Up',
                'type' => 'pengangkut_barang',
                'owner' => 'inhouse',
                'bbm' => 12.5,
                'next_service_date' => '2024-11-10',
            ],
            [
                'plate_number' => 'B 7890 MNO',
                'model' => 'Suzuki Carry Pick Up',
                'type' => 'pengangkut_barang',
                'owner' => 'rental',
                'bbm' => 14.2,
                'next_service_date' => '2024-12-05',
            ],
        ];

        // Create 2 vehicles for people transport (pengangkut_orang)
        $peopleVehicles = [
            [
                'plate_number' => 'B 1111 PQR',
                'model' => 'Toyota Hiace',
                'type' => 'pengangkut_orang',
                'owner' => 'inhouse',
                'bbm' => 9.5,
                'next_service_date' => '2024-11-25',
            ],
            [
                'plate_number' => 'B 2222 STU',
                'model' => 'Isuzu Elf Microbus',
                'type' => 'pengangkut_orang',
                'owner' => 'rental',
                'bbm' => 8.8,
                'next_service_date' => '2024-12-10',
            ],
        ];

        // Insert goods vehicles
        foreach ($goodsVehicles as $vehicle) {
            Vehicles::create($vehicle);
        }

        // Insert people vehicles
        foreach ($peopleVehicles as $vehicle) {
            Vehicles::create($vehicle);
        }

        echo "7 vehicles created successfully:\n";
        echo "- 5 vehicles for goods transport (pengangkut_barang)\n";
        echo "- 2 vehicles for people transport (pengangkut_orang)\n";
        echo "Vehicle types: inhouse and rental\n";
    }
}