<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile; // Importante para pegar os IDs de perfil
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR'); // Configurado para dados brasileiros (CPF, etc)

        // Pega todos os IDs de perfis existentes para relacionar
        $profileIds = Profile::pluck('id')->toArray();

        // Se não houver perfis, avisa o console
        if (empty($profileIds)) {
            $this->command->error('Nenhum perfil encontrado! Crie perfis antes de rodar o UserSeeder.');
            return;
        }

        for ($i = 0; $i < 5; $i++) {
            User::create([
                'name'              => $faker->name,
                'email'             => $faker->unique()->safeEmail,
                'cpf'               => $faker->unique()->cpf,
                'profile_id'        => $faker->randomElement($profileIds),
                'data_registration' => $faker->dateTimeBetween('2025-12-01', '2025-12-22')->format('Y/m/d'),
            ]);
        }
    }
}
