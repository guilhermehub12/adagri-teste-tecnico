<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\User;

use App\Models\Propriedade;
use App\Models\UnidadeProducao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelatorioHectaresCulturaTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaders(UserRole $role = UserRole::EXTENSIONISTA): array
    {
        $user = User::factory()->create(['role' => $role]);
        $token = $user->createToken('test')->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_endpoint_retorna_200_com_dados_agrupados(): void
    {
        UnidadeProducao::factory()->count(3)->create(['nome_cultura' => 'Milho']);

        $response = $this->getJson('/api/relatorios/hectares-por-cultura', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'cultura',
                    'total_hectares',
                    'total_unidades',
                    'total_propriedades',
                    'media_hectares_por_unidade',
                ],
            ],
        ]);
    }

    public function test_agrupa_unidades_por_cultura_corretamente(): void
    {
        UnidadeProducao::factory()->count(3)->create(['nome_cultura' => 'Milho']);
        UnidadeProducao::factory()->count(2)->create(['nome_cultura' => 'Soja']);
        UnidadeProducao::factory()->create(['nome_cultura' => 'Feijão']);

        $response = $this->getJson('/api/relatorios/hectares-por-cultura', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');

        $culturas = collect($response->json('data'))->pluck('cultura')->toArray();
        $this->assertContains('Milho', $culturas);
        $this->assertContains('Soja', $culturas);
        $this->assertContains('Feijão', $culturas);
    }

    public function test_calcula_total_de_hectares_por_cultura(): void
    {
        UnidadeProducao::factory()->create(['nome_cultura' => 'Milho', 'area_total_ha' => 50.5]);
        UnidadeProducao::factory()->create(['nome_cultura' => 'Milho', 'area_total_ha' => 75.25]);
        UnidadeProducao::factory()->create(['nome_cultura' => 'Soja', 'area_total_ha' => 120.0]);

        $response = $this->getJson('/api/relatorios/hectares-por-cultura', $this->authHeaders());

        $response->assertStatus(200);

        $milho = collect($response->json('data'))->firstWhere('cultura', 'Milho');
        $soja = collect($response->json('data'))->firstWhere('cultura', 'Soja');

        $this->assertEquals(125.75, $milho['total_hectares']);
        $this->assertEquals(120.0, $soja['total_hectares']);
    }

    public function test_calcula_total_de_unidades_por_cultura(): void
    {
        UnidadeProducao::factory()->count(7)->create(['nome_cultura' => 'Milho']);
        UnidadeProducao::factory()->count(4)->create(['nome_cultura' => 'Soja']);

        $response = $this->getJson('/api/relatorios/hectares-por-cultura', $this->authHeaders());

        $response->assertStatus(200);

        $milho = collect($response->json('data'))->firstWhere('cultura', 'Milho');
        $soja = collect($response->json('data'))->firstWhere('cultura', 'Soja');

        $this->assertEquals(7, $milho['total_unidades']);
        $this->assertEquals(4, $soja['total_unidades']);
    }

    public function test_conta_propriedades_unicas_por_cultura(): void
    {
        $propriedade1 = Propriedade::factory()->create();
        $propriedade2 = Propriedade::factory()->create();
        $propriedade3 = Propriedade::factory()->create();

        // 3 unidades de Milho em 2 propriedades únicas
        UnidadeProducao::factory()->create(['nome_cultura' => 'Milho', 'propriedade_id' => $propriedade1->id]);
        UnidadeProducao::factory()->create(['nome_cultura' => 'Milho', 'propriedade_id' => $propriedade1->id]);
        UnidadeProducao::factory()->create(['nome_cultura' => 'Milho', 'propriedade_id' => $propriedade2->id]);

        // 2 unidades de Soja em 2 propriedades únicas
        UnidadeProducao::factory()->create(['nome_cultura' => 'Soja', 'propriedade_id' => $propriedade2->id]);
        UnidadeProducao::factory()->create(['nome_cultura' => 'Soja', 'propriedade_id' => $propriedade3->id]);

        $response = $this->getJson('/api/relatorios/hectares-por-cultura', $this->authHeaders());

        $response->assertStatus(200);

        $milho = collect($response->json('data'))->firstWhere('cultura', 'Milho');
        $soja = collect($response->json('data'))->firstWhere('cultura', 'Soja');

        $this->assertEquals(2, $milho['total_propriedades']);
        $this->assertEquals(2, $soja['total_propriedades']);
    }

    public function test_calcula_media_de_hectares_por_unidade(): void
    {
        UnidadeProducao::factory()->create(['nome_cultura' => 'Milho', 'area_total_ha' => 100.0]);
        UnidadeProducao::factory()->create(['nome_cultura' => 'Milho', 'area_total_ha' => 200.0]);
        UnidadeProducao::factory()->create(['nome_cultura' => 'Milho', 'area_total_ha' => 300.0]);

        $response = $this->getJson('/api/relatorios/hectares-por-cultura', $this->authHeaders());

        $response->assertStatus(200);

        $milho = collect($response->json('data'))->firstWhere('cultura', 'Milho');

        $this->assertEquals(200.0, $milho['media_hectares_por_unidade']);
    }

    public function test_ordena_culturas_alfabeticamente(): void
    {
        UnidadeProducao::factory()->create(['nome_cultura' => 'Soja']);
        UnidadeProducao::factory()->create(['nome_cultura' => 'Feijão']);
        UnidadeProducao::factory()->create(['nome_cultura' => 'Milho']);

        $response = $this->getJson('/api/relatorios/hectares-por-cultura', $this->authHeaders());

        $response->assertStatus(200);

        $culturas = collect($response->json('data'))->pluck('cultura')->toArray();

        $this->assertEquals(['Feijão', 'Milho', 'Soja'], $culturas);
    }

    public function test_pode_filtrar_por_cultura_especifica(): void
    {
        UnidadeProducao::factory()->count(5)->create(['nome_cultura' => 'Milho', 'area_total_ha' => 50.0]);
        UnidadeProducao::factory()->count(3)->create(['nome_cultura' => 'Soja', 'area_total_ha' => 80.0]);

        $response = $this->getJson('/api/relatorios/hectares-por-cultura?cultura=Milho', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

        $resultado = $response->json('data.0');
        $this->assertEquals('Milho', $resultado['cultura']);
        $this->assertEquals(250.0, $resultado['total_hectares']);
        $this->assertEquals(5, $resultado['total_unidades']);
    }

    public function test_retorna_array_vazio_quando_nao_ha_dados(): void
    {
        $response = $this->getJson('/api/relatorios/hectares-por-cultura', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    public function test_estrutura_json_do_relatorio_esta_correta(): void
    {
        UnidadeProducao::factory()->create([
            'nome_cultura' => 'Milho',
            'area_total_ha' => 125.5,
        ]);

        $response = $this->getJson('/api/relatorios/hectares-por-cultura', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'cultura',
                    'total_hectares',
                    'total_unidades',
                    'total_propriedades',
                    'media_hectares_por_unidade',
                ],
            ],
        ]);

        $resultado = $response->json('data.0');

        $this->assertIsString($resultado['cultura']);
        $this->assertIsNumeric($resultado['total_hectares']);
        $this->assertIsInt($resultado['total_unidades']);
        $this->assertIsInt($resultado['total_propriedades']);
        $this->assertIsNumeric($resultado['media_hectares_por_unidade']);
    }
}
