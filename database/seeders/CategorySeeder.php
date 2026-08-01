<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'natan@teste.com')->first();

        Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Salário',
            'type' => 'income',
        ]);

        Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Alimentação',
            'type' => 'expense',
        ]);
    }
}
