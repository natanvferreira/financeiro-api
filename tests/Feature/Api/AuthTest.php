<?php

use App\Models\User;

it('registra um novo usuario e retorna um token', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Natan',
        'email' => 'natan@teste.com',
        'password' => 'senha123',
        'password_confirmation' => 'senha123',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['user', 'token']);

    $this->assertDatabaseHas('users', [
        'email' => 'natan@teste.com',
    ]);
});

it('nao registra com email duplicado', function () {
    User::factory()->create(['email' => 'natan@teste.com']);

    $response = $this->postJson('/api/register', [
        'name' => 'Outro',
        'email' => 'natan@teste.com',
        'password' => 'senha123',
        'password_confirmation' => 'senha123',
    ]);

    $response->assertStatus(422);
});
