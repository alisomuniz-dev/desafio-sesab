<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            ProfileSeeder::class,
            UserSeeder::class,
        ]);

        $addresses = Address::factory(10)->create();

        $users = User::all();

        $users->each(function ($user) use ($addresses) {

            $randomAddresses = $addresses->random(rand(1, 2))->pluck('id');

            $user->addresses()->attach($randomAddresses);
        });
    }
}
