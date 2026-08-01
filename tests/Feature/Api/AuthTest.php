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

it('bloqueia apos muitas tentativas de login', function () {
    for ($i = 0; $i < 6; $i++) {
        $this->postJson('/api/login', [
            'email' => 'errado@teste.com',
            'password' => 'errada',
        ]);
    }

    $response = $this->postJson('/api/login', [
        'email' => 'errado@teste.com',
        'password' => 'errada',
    ]);

    $response->assertStatus(429);
});
