<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\User;

use App\Models\ProdutorRural;
use App\Models\Propriedade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelatorioPropriedadesMunicipioTest extends TestCase
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
        Propriedade::factory()->count(3)->create(['municipio' => 'Fortaleza', 'uf' => 'CE']);

        $response = $this->getJson('/api/relatorios/propriedades-por-municipio', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'municipio',
                    'uf',
                    'total_propriedades',
                    'area_total_ha',
                    'total_produtores',
                ],
            ],
        ]);
    }

    public function test_agrupa_propriedades_por_municipio_corretamente(): void
    {
        Propriedade::factory()->count(3)->create(['municipio' => 'Fortaleza', 'uf' => 'CE']);
        Propriedade::factory()->count(2)->create(['municipio' => 'Caucaia', 'uf' => 'CE']);

        $response = $this->getJson('/api/relatorios/propriedades-por-municipio', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    public function test_calcula_total_de_propriedades_por_municipio(): void
    {
        Propriedade::factory()->count(5)->create(['municipio' => 'Fortaleza', 'uf' => 'CE']);
        Propriedade::factory()->count(3)->create(['municipio' => 'Caucaia', 'uf' => 'CE']);

        $response = $this->getJson('/api/relatorios/propriedades-por-municipio', $this->authHeaders());

        $response->assertStatus(200);

        $fortaleza = collect($response->json('data'))->firstWhere('municipio', 'Fortaleza');
        $caucaia = collect($response->json('data'))->firstWhere('municipio', 'Caucaia');

        $this->assertEquals(5, $fortaleza['total_propriedades']);
        $this->assertEquals(3, $caucaia['total_propriedades']);
    }

    public function test_calcula_area_total_por_municipio(): void
    {
        Propriedade::factory()->create(['municipio' => 'Fortaleza', 'uf' => 'CE', 'area_total' => 100.50]);
        Propriedade::factory()->create(['municipio' => 'Fortaleza', 'uf' => 'CE', 'area_total' => 200.75]);
        Propriedade::factory()->create(['municipio' => 'Caucaia', 'uf' => 'CE', 'area_total' => 150.25]);

        $response = $this->getJson('/api/relatorios/propriedades-por-municipio', $this->authHeaders());

        $response->assertStatus(200);

        $fortaleza = collect($response->json('data'))->firstWhere('municipio', 'Fortaleza');
        $caucaia = collect($response->json('data'))->firstWhere('municipio', 'Caucaia');

        $this->assertEquals(301.25, $fortaleza['area_total_ha']);
        $this->assertEquals(150.25, $caucaia['area_total_ha']);
    }

    public function test_conta_produtores_unicos_por_municipio(): void
    {
        $produtor1 = ProdutorRural::factory()->create();
        $produtor2 = ProdutorRural::factory()->create();
        $produtor3 = ProdutorRural::factory()->create();

        // 3 propriedades em Fortaleza, mas apenas 2 produtores únicos
        Propriedade::factory()->create(['municipio' => 'Fortaleza', 'uf' => 'CE', 'produtor_id' => $produtor1->id]);
        Propriedade::factory()->create(['municipio' => 'Fortaleza', 'uf' => 'CE', 'produtor_id' => $produtor1->id]);
        Propriedade::factory()->create(['municipio' => 'Fortaleza', 'uf' => 'CE', 'produtor_id' => $produtor2->id]);

        // 1 propriedade em Caucaia com 1 produtor
        Propriedade::factory()->create(['municipio' => 'Caucaia', 'uf' => 'CE', 'produtor_id' => $produtor3->id]);

        $response = $this->getJson('/api/relatorios/propriedades-por-municipio', $this->authHeaders());

        $response->assertStatus(200);

        $fortaleza = collect($response->json('data'))->firstWhere('municipio', 'Fortaleza');
        $caucaia = collect($response->json('data'))->firstWhere('municipio', 'Caucaia');

        $this->assertEquals(2, $fortaleza['total_produtores']);
        $this->assertEquals(1, $caucaia['total_produtores']);
    }

    public function test_ordena_municipios_alfabeticamente(): void
    {
        Propriedade::factory()->create(['municipio' => 'Fortaleza', 'uf' => 'CE']);
        Propriedade::factory()->create(['municipio' => 'Caucaia', 'uf' => 'CE']);
        Propriedade::factory()->create(['municipio' => 'Aquiraz', 'uf' => 'CE']);

        $response = $this->getJson('/api/relatorios/propriedades-por-municipio', $this->authHeaders());

        $response->assertStatus(200);

        $municipios = collect($response->json('data'))->pluck('municipio')->toArray();

        $this->assertEquals(['Aquiraz', 'Caucaia', 'Fortaleza'], $municipios);
    }

    public function test_pode_filtrar_por_uf(): void
    {
        Propriedade::factory()->count(3)->create(['municipio' => 'Fortaleza', 'uf' => 'CE']);
        Propriedade::factory()->count(2)->create(['municipio' => 'São Paulo', 'uf' => 'SP']);

        $response = $this->getJson('/api/relatorios/propriedades-por-municipio?uf=CE', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

        $resultado = $response->json('data.0');
        $this->assertEquals('CE', $resultado['uf']);
        $this->assertEquals('Fortaleza', $resultado['municipio']);
    }

    public function test_pode_filtrar_por_municipio_especifico(): void
    {
        Propriedade::factory()->count(3)->create(['municipio' => 'Fortaleza', 'uf' => 'CE']);
        Propriedade::factory()->count(2)->create(['municipio' => 'Caucaia', 'uf' => 'CE']);

        $response = $this->getJson('/api/relatorios/propriedades-por-municipio?municipio=Fortaleza', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

        $resultado = $response->json('data.0');
        $this->assertEquals('Fortaleza', $resultado['municipio']);
        $this->assertEquals(3, $resultado['total_propriedades']);
    }

    public function test_retorna_array_vazio_quando_nao_ha_dados(): void
    {
        $response = $this->getJson('/api/relatorios/propriedades-por-municipio', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    public function test_estrutura_json_do_relatorio_esta_correta(): void
    {
        Propriedade::factory()->create([
            'municipio' => 'Fortaleza',
            'uf' => 'CE',
            'area_total' => 100.50,
        ]);

        $response = $this->getJson('/api/relatorios/propriedades-por-municipio', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'municipio',
                    'uf',
                    'total_propriedades',
                    'area_total_ha',
                    'total_produtores',
                ],
            ],
        ]);

        $resultado = $response->json('data.0');

        $this->assertIsString($resultado['municipio']);
        $this->assertIsString($resultado['uf']);
        $this->assertIsInt($resultado['total_propriedades']);
        $this->assertIsNumeric($resultado['area_total_ha']);
        $this->assertIsInt($resultado['total_produtores']);
    }
}
