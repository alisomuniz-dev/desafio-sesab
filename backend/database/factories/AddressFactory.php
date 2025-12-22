<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'logradouro' => $this->faker->streetAddress(),
            'cep' => $this->faker->postcode(), // O Faker gera formatos variados, você pode usar regex se precisar de um formato fixo
        ];
    }
}
