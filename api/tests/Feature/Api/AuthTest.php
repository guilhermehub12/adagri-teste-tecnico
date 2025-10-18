<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_pode_se_registrar(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'João Silva',
            'email' => 'joao@email.com',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'user' => ['id', 'name', 'email'],
            'token',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'joao@email.com',
        ]);
    }

    public function test_registro_requer_nome_email_e_senha(): void
    {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_registro_requer_email_valido(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'João Silva',
            'email' => 'email-invalido',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_registro_requer_email_unico(): void
    {
        User::factory()->create(['email' => 'joao@email.com']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'João Silva',
            'email' => 'joao@email.com',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_registro_requer_senha_minima(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'João Silva',
            'email' => 'joao@email.com',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_usuario_pode_fazer_login(): void
    {
        $user = User::factory()->create([
            'email' => 'joao@email.com',
            'password' => bcrypt('senha123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'joao@email.com',
            'password' => 'senha123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => ['id', 'name', 'email'],
            'token',
        ]);
    }

    public function test_login_retorna_token(): void
    {
        $user = User::factory()->create([
            'email' => 'joao@email.com',
            'password' => bcrypt('senha123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'joao@email.com',
            'password' => 'senha123',
        ]);

        $response->assertStatus(200);
        $this->assertNotNull($response->json('token'));
        $this->assertIsString($response->json('token'));
    }

    public function test_login_falha_com_credenciais_invalidas(): void
    {
        $user = User::factory()->create([
            'email' => 'joao@email.com',
            'password' => bcrypt('senha123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'joao@email.com',
            'password' => 'senha-errada',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid credentials',
        ]);
    }

    public function test_usuario_autenticado_pode_acessar_perfil(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->getJson('/api/auth/me', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function test_usuario_nao_autenticado_nao_pode_acessar_perfil(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    public function test_usuario_pode_fazer_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Logged out successfully',
        ]);
    }

    public function test_logout_revoga_token_atual(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Verificar que o usuário tem 1 token
        $this->assertEquals(1, $user->tokens()->count());

        // Fazer logout
        $this->postJson('/api/auth/logout', [], [
            'Authorization' => "Bearer {$token}",
        ]);

        // Verificar que o token foi deletado
        $this->assertEquals(0, $user->tokens()->count());
    }
}
