<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminWithToken(): array
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $token = $admin->createToken('test')->plainTextToken;

        return [$admin, $token];
    }

    public function test_admin_pode_listar_usuarios(): void
    {
        [$admin, $token] = $this->createAdminWithToken();

        // Criar alguns usuários de teste
        User::factory()->create(['role' => UserRole::GESTOR]);
        User::factory()->create(['role' => UserRole::TECNICO]);
        User::factory()->create(['role' => UserRole::EXTENSIONISTA]);

        $response = $this->getJson('/api/users', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'email', 'role', 'role_label', 'created_at']
                ]
            ]);
    }

    public function test_admin_pode_criar_usuario_com_role_gestor(): void
    {
        [$admin, $token] = $this->createAdminWithToken();

        $response = $this->postJson('/api/users', [
            'name' => 'Novo Gestor',
            'email' => 'novogestor@adagri.ce.gov.br',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'gestor',
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'user' => [
                    'name' => 'Novo Gestor',
                    'email' => 'novogestor@adagri.ce.gov.br',
                    'role' => 'gestor',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'novogestor@adagri.ce.gov.br',
            'role' => 'gestor',
        ]);
    }

    public function test_admin_pode_criar_usuario_com_role_tecnico(): void
    {
        [$admin, $token] = $this->createAdminWithToken();

        $response = $this->postJson('/api/users', [
            'name' => 'Novo Técnico',
            'email' => 'novotecnico@adagri.ce.gov.br',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'tecnico',
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(201);

        $user = User::where('email', 'novotecnico@adagri.ce.gov.br')->first();
        $this->assertEquals(UserRole::TECNICO, $user->role);
    }

    public function test_admin_pode_criar_usuario_com_role_extensionista(): void
    {
        [$admin, $token] = $this->createAdminWithToken();

        $response = $this->postJson('/api/users', [
            'name' => 'Novo Extensionista',
            'email' => 'novoext@adagri.ce.gov.br',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'extensionista',
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(201);

        $user = User::where('email', 'novoext@adagri.ce.gov.br')->first();
        $this->assertEquals(UserRole::EXTENSIONISTA, $user->role);
    }

    public function test_admin_pode_criar_outro_admin(): void
    {
        [$admin, $token] = $this->createAdminWithToken();

        $response = $this->postJson('/api/users', [
            'name' => 'Novo Admin',
            'email' => 'novoadmin@adagri.ce.gov.br',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(201);

        $user = User::where('email', 'novoadmin@adagri.ce.gov.br')->first();
        $this->assertEquals(UserRole::ADMIN, $user->role);
    }

    public function test_admin_pode_ver_detalhes_de_usuario(): void
    {
        [$admin, $token] = $this->createAdminWithToken();
        $user = User::factory()->create(['role' => UserRole::GESTOR]);

        $response = $this->getJson("/api/users/{$user->id}", [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => 'gestor',
                ]
            ]);
    }

    public function test_admin_pode_atualizar_usuario(): void
    {
        [$admin, $token] = $this->createAdminWithToken();
        $user = User::factory()->create(['role' => UserRole::EXTENSIONISTA]);

        $response = $this->putJson("/api/users/{$user->id}", [
            'name' => 'Nome Atualizado',
            'email' => $user->email,
            'role' => 'gestor',
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nome Atualizado',
            'role' => 'gestor',
        ]);
    }

    public function test_admin_pode_atualizar_senha_de_usuario(): void
    {
        [$admin, $token] = $this->createAdminWithToken();
        $user = User::factory()->create(['role' => UserRole::TECNICO]);

        $response = $this->putJson("/api/users/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'role' => 'tecnico',
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200);

        // Verificar se a senha foi atualizada
        $user->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $user->password));
    }

    public function test_admin_pode_deletar_usuario(): void
    {
        [$admin, $token] = $this->createAdminWithToken();
        $user = User::factory()->create(['role' => UserRole::EXTENSIONISTA]);

        $response = $this->deleteJson("/api/users/{$user->id}", [], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_gestor_nao_pode_listar_usuarios(): void
    {
        $gestor = User::factory()->create(['role' => UserRole::GESTOR]);
        $token = $gestor->createToken('test')->plainTextToken;

        $response = $this->getJson('/api/users', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(403);
    }

    public function test_gestor_nao_pode_criar_usuario(): void
    {
        $gestor = User::factory()->create(['role' => UserRole::GESTOR]);
        $token = $gestor->createToken('test')->plainTextToken;

        $response = $this->postJson('/api/users', [
            'name' => 'Teste',
            'email' => 'teste@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'tecnico',
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(403);
    }

    public function test_tecnico_nao_pode_atualizar_usuarios(): void
    {
        $tecnico = User::factory()->create(['role' => UserRole::TECNICO]);
        $token = $tecnico->createToken('test')->plainTextToken;

        $user = User::factory()->create(['role' => UserRole::EXTENSIONISTA]);

        $response = $this->putJson("/api/users/{$user->id}", [
            'name' => 'Novo Nome',
            'email' => $user->email,
            'role' => 'gestor',
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(403);
    }

    public function test_extensionista_nao_pode_deletar_usuarios(): void
    {
        $extensionista = User::factory()->create(['role' => UserRole::EXTENSIONISTA]);
        $token = $extensionista->createToken('test')->plainTextToken;

        $user = User::factory()->create(['role' => UserRole::TECNICO]);

        $response = $this->deleteJson("/api/users/{$user->id}", [], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(403);
    }

    public function test_criacao_de_usuario_requer_role(): void
    {
        [$admin, $token] = $this->createAdminWithToken();

        $response = $this->postJson('/api/users', [
            'name' => 'Usuário sem role',
            'email' => 'semrole@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_criacao_de_usuario_requer_email_unico(): void
    {
        [$admin, $token] = $this->createAdminWithToken();
        $existingUser = User::factory()->create(['email' => 'existe@test.com']);

        $response = $this->postJson('/api/users', [
            'name' => 'Novo Usuário',
            'email' => 'existe@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'tecnico',
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_criacao_de_usuario_requer_role_valido(): void
    {
        [$admin, $token] = $this->createAdminWithToken();

        $response = $this->postJson('/api/users', [
            'name' => 'Teste',
            'email' => 'teste@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'invalid_role',
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_usuario_nao_autenticado_nao_pode_acessar_gerenciamento(): void
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }
}
