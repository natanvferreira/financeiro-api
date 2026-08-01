<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Natan',
            'email' => 'natan@teste.com',
            'password' => bcrypt('senha123'),
        ]);

        $this->call([
            CategorySeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
