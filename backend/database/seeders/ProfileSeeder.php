<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Profile;
use Faker\Factory as Faker;

class ProfileSeeder extends Seeder
{

    public function run(): void
    {
        $faker = Faker::create('pt_BR'); // Configurado para dados brasileiros (CPF, etc)

        Profile::create([
            'name' => 'admin',
            'data_registration' => $faker->dateTimeBetween('2025-12-19', '2025-12-20')->format('Y/m/d'),
        ]);
        Profile::create([
            'name' => 'comun',
            'data_registration' => $faker->dateTimeBetween('2025-12-21', '2025-12-22')->format('Y/m/d'),
        ]);
    }
}
