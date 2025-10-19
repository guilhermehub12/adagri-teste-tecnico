<?php

namespace Tests\Feature\Api;

use App\Models\ProdutorRural;
use App\Models\Propriedade;
use App\Models\Rebanho;
use App\Models\UnidadeProducao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_autenticado_pode_acessar_estatisticas_dashboard(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->getJson('/api/dashboard/stats', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'totalProdutores',
            'totalPropriedades',
            'totalAnimais',
            'totalHectares',
        ]);
    }

    public function test_usuario_nao_autenticado_nao_pode_acessar_estatisticas(): void
    {
        $response = $this->getJson('/api/dashboard/stats');

        $response->assertStatus(401);
    }

    public function test_retorna_contagem_correta_de_produtores(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Criar 3 produtores rurais
        ProdutorRural::factory()->count(3)->create();

        $response = $this->getJson('/api/dashboard/stats', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'totalProdutores' => 3,
        ]);
    }

    public function test_retorna_contagem_correta_de_propriedades(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Criar produtor e propriedades
        $produtor = ProdutorRural::factory()->create();
        Propriedade::factory()->count(5)->create([
            'produtor_id' => $produtor->id,
        ]);

        $response = $this->getJson('/api/dashboard/stats', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'totalPropriedades' => 5,
        ]);
    }

    public function test_retorna_soma_correta_de_animais(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Criar produtor e propriedade
        $produtor = ProdutorRural::factory()->create();
        $propriedade = Propriedade::factory()->create([
            'produtor_id' => $produtor->id,
        ]);

        // Criar rebanhos com diferentes quantidades
        Rebanho::factory()->create([
            'propriedade_id' => $propriedade->id,
            'quantidade' => 100,
        ]);
        Rebanho::factory()->create([
            'propriedade_id' => $propriedade->id,
            'quantidade' => 250,
        ]);
        Rebanho::factory()->create([
            'propriedade_id' => $propriedade->id,
            'quantidade' => 50,
        ]);

        $response = $this->getJson('/api/dashboard/stats', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'totalAnimais' => 400, // 100 + 250 + 50
        ]);
    }

    public function test_retorna_soma_correta_de_hectares(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Criar produtor e propriedade
        $produtor = ProdutorRural::factory()->create();
        $propriedade = Propriedade::factory()->create([
            'produtor_id' => $produtor->id,
        ]);

        // Criar unidades de produção com diferentes áreas
        UnidadeProducao::factory()->create([
            'propriedade_id' => $propriedade->id,
            'area_total_ha' => 150.50,
        ]);
        UnidadeProducao::factory()->create([
            'propriedade_id' => $propriedade->id,
            'area_total_ha' => 200.75,
        ]);
        UnidadeProducao::factory()->create([
            'propriedade_id' => $propriedade->id,
            'area_total_ha' => 75.25,
        ]);

        $response = $this->getJson('/api/dashboard/stats', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'totalHectares' => 426.50, // 150.50 + 200.75 + 75.25
        ]);
    }

    public function test_retorna_zeros_quando_nao_ha_dados(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->getJson('/api/dashboard/stats', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'totalProdutores' => 0,
            'totalPropriedades' => 0,
            'totalAnimais' => 0,
            'totalHectares' => 0,
        ]);
    }

    public function test_endpoint_nao_expoe_dados_sensiveis(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Criar produtor com dados sensíveis
        ProdutorRural::factory()->create([
            'nome' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'email' => 'joao@email.com',
            'telefone' => '(11) 99999-9999',
        ]);

        $response = $this->getJson('/api/dashboard/stats', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200);

        // Garantir que não retorna dados sensíveis
        $response->assertJsonMissing(['nome', 'cpf_cnpj', 'email', 'telefone']);

        // Garantir que retorna apenas estatísticas numéricas
        $this->assertIsInt($response->json('totalProdutores'));
        $this->assertIsInt($response->json('totalPropriedades'));
        $this->assertIsInt($response->json('totalAnimais'));
        $this->assertTrue(
            is_int($response->json('totalHectares')) || is_float($response->json('totalHectares'))
        );
    }
}
