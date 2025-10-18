<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\Propriedade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pode_criar_recurso(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $token = $admin->createToken('test')->plainTextToken;

        $response = $this->postJson('/api/propriedades', [
            'nome' => 'Test Farm',
            'municipio' => 'Fortaleza',
            'uf' => 'CE',
            'area_total' => 100.5,
            'produtor_id' => 1,
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $this->assertTrue($admin->canCreate());
    }

    public function test_admin_pode_editar_recurso(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $this->assertTrue($admin->canEdit());
    }

    public function test_admin_pode_deletar_recurso(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $this->assertTrue($admin->canDelete());
    }

    public function test_admin_pode_gerenciar_usuarios(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $this->assertTrue($admin->canManageUsers());
    }

    public function test_gestor_pode_criar_e_editar(): void
    {
        $gestor = User::factory()->create(['role' => UserRole::GESTOR]);

        $this->assertTrue($gestor->canCreate());
        $this->assertTrue($gestor->canEdit());
    }

    public function test_gestor_nao_pode_deletar(): void
    {
        $gestor = User::factory()->create(['role' => UserRole::GESTOR]);

        $this->assertFalse($gestor->canDelete());
    }

    public function test_gestor_nao_pode_gerenciar_usuarios(): void
    {
        $gestor = User::factory()->create(['role' => UserRole::GESTOR]);

        $this->assertFalse($gestor->canManageUsers());
    }

    public function test_tecnico_pode_criar(): void
    {
        $tecnico = User::factory()->create(['role' => UserRole::TECNICO]);

        $this->assertTrue($tecnico->canCreate());
    }

    public function test_tecnico_nao_pode_editar(): void
    {
        $tecnico = User::factory()->create(['role' => UserRole::TECNICO]);

        $this->assertFalse($tecnico->canEdit());
    }

    public function test_tecnico_nao_pode_deletar(): void
    {
        $tecnico = User::factory()->create(['role' => UserRole::TECNICO]);

        $this->assertFalse($tecnico->canDelete());
    }

    public function test_extensionista_nao_pode_criar(): void
    {
        $extensionista = User::factory()->create(['role' => UserRole::EXTENSIONISTA]);

        $this->assertFalse($extensionista->canCreate());
    }

    public function test_extensionista_nao_pode_editar(): void
    {
        $extensionista = User::factory()->create(['role' => UserRole::EXTENSIONISTA]);

        $this->assertFalse($extensionista->canEdit());
    }

    public function test_extensionista_nao_pode_deletar(): void
    {
        $extensionista = User::factory()->create(['role' => UserRole::EXTENSIONISTA]);

        $this->assertFalse($extensionista->canDelete());
    }

    public function test_extensionista_pode_acessar_relatorios(): void
    {
        $extensionista = User::factory()->create(['role' => UserRole::EXTENSIONISTA]);
        $token = $extensionista->createToken('test')->plainTextToken;

        $response = $this->getJson('/api/relatorios/propriedades-por-municipio', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200);
    }

    public function test_registro_cria_usuario_como_extensionista_por_padrao(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);

        $user = User::where('email', 'test@test.com')->first();
        $this->assertEquals(UserRole::EXTENSIONISTA, $user->role);
    }

    public function test_registro_pode_criar_usuario_com_role_especifico(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test Admin',
            'email' => 'testadmin@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertStatus(201);

        $user = User::where('email', 'testadmin@test.com')->first();
        $this->assertEquals(UserRole::ADMIN, $user->role);
    }

    public function test_registro_pode_criar_usuario_com_diferentes_roles(): void
    {
        // Testa criação com role gestor
        $response1 = $this->postJson('/api/auth/register', [
            'name' => 'Test Gestor',
            'email' => 'gestor@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'gestor',
        ]);

        $response1->assertStatus(201);
        $user1 = User::where('email', 'gestor@test.com')->first();
        $this->assertEquals(UserRole::GESTOR, $user1->role);

        // Testa criação com role tecnico
        $response2 = $this->postJson('/api/auth/register', [
            'name' => 'Test Técnico',
            'email' => 'tecnico@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'tecnico',
        ]);

        $response2->assertStatus(201);
        $user2 = User::where('email', 'tecnico@test.com')->first();
        $this->assertEquals(UserRole::TECNICO, $user2->role);

        // Testa criação com role extensionista explícito
        $response3 = $this->postJson('/api/auth/register', [
            'name' => 'Test Extensionista',
            'email' => 'extensionista@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'extensionista',
        ]);

        $response3->assertStatus(201);
        $user3 = User::where('email', 'extensionista@test.com')->first();
        $this->assertEquals(UserRole::EXTENSIONISTA, $user3->role);
    }
}
