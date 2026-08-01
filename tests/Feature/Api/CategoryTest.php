<?php

use App\Models\User;
use App\Models\Category;

it('lista apenas as categorias do usuario autenticado', function () {
    $user = User::factory()->create();
    $outroUser = User::factory()->create();

    Category::factory()->create(['user_id' => $user->id, 'name' => 'Minha categoria']);
    Category::factory()->create(['user_id' => $outroUser->id, 'name' => 'Categoria de outro usuario']);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/categories');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['name' => 'Minha categoria']);
});

it('cria uma categoria para o usuario autenticado', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/categories', [
            'name' => 'Transporte',
            'type' => 'expense',
        ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['name' => 'Transporte']);

    $this->assertDatabaseHas('categories', [
        'name' => 'Transporte',
        'user_id' => $user->id,
    ]);
});

it('nao permite criar categoria sem autenticacao', function () {
    $response = $this->postJson('/api/categories', [
        'name' => 'Transporte',
        'type' => 'expense',
    ]);

    $response->assertStatus(401);
});

it('nao permite deletar categoria de outro usuario', function () {
    $user = User::factory()->create();
    $outroUser = User::factory()->create();

    $category = Category::factory()->create(['user_id' => $outroUser->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/categories/{$category->id}");

    $response->assertStatus(403);

    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});
