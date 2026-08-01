<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Transaction;

it('lista apenas as transacoes do usuario autenticado', function () {
    $user = User::factory()->create();
    $outroUser = User::factory()->create();

    Transaction::factory()->create(['user_id' => $user->id, 'description' => 'Minha transacao']);
    Transaction::factory()->create(['user_id' => $outroUser->id, 'description' => 'Transacao de outro']);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/transactions');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['description' => 'Minha transacao']);
});

it('cria uma transacao vinculada a uma categoria do usuario', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/transactions', [
            'description' => 'Supermercado',
            'amount' => 350.50,
            'date' => '2026-08-01',
            'category_id' => $category->id,
        ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['description' => 'Supermercado']);

    $this->assertDatabaseHas('transactions', [
        'description' => 'Supermercado',
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);
});

it('nao cria transacao com categoria inexistente', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/transactions', [
            'description' => 'Supermercado',
            'amount' => 350.50,
            'date' => '2026-08-01',
            'category_id' => 999,
        ]);

    $response->assertStatus(422);
});

it('filtra transacoes por categoria', function () {
    $user = User::factory()->create();
    $categoriaA = Category::factory()->create(['user_id' => $user->id]);
    $categoriaB = Category::factory()->create(['user_id' => $user->id]);

    Transaction::factory()->create(['user_id' => $user->id, 'category_id' => $categoriaA->id]);
    Transaction::factory()->create(['user_id' => $user->id, 'category_id' => $categoriaB->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/transactions?category_id={$categoriaA->id}");

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});
