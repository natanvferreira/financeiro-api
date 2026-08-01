<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'natan@teste.com')->first();
        $income = Category::where('user_id', $user->id)->where('type', 'income')->first();
        $expense = Category::where('user_id', $user->id)->where('type', 'expense')->first();

        Transaction::factory()->create([
            'user_id' => $user->id,
            'category_id' => $income->id,
            'description' => 'Salário de agosto',
            'amount' => 5000,
            'date' => now(),
        ]);

        Transaction::factory()->count(10)->create([
            'user_id' => $user->id,
            'category_id' => $expense->id,
        ]);
    }
}
