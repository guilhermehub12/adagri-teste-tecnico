<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\User;

use App\Models\Propriedade;
use App\Models\Rebanho;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelatorioAnimaisEspecieTest extends TestCase
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
        Rebanho::factory()->count(3)->create(['especie' => 'Bovino']);

        $response = $this->getJson('/api/relatorios/animais-por-especie', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'especie',
                    'total_animais',
                    'total_rebanhos',
                    'total_propriedades',
                    'media_animais_por_rebanho',
                ],
            ],
        ]);
    }

    public function test_agrupa_animais_por_especie_corretamente(): void
    {
        Rebanho::factory()->count(3)->create(['especie' => 'Bovino']);
        Rebanho::factory()->count(2)->create(['especie' => 'Caprino']);
        Rebanho::factory()->create(['especie' => 'Ovino']);

        $response = $this->getJson('/api/relatorios/animais-por-especie', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');

        $especies = collect($response->json('data'))->pluck('especie')->toArray();
        $this->assertContains('Bovino', $especies);
        $this->assertContains('Caprino', $especies);
        $this->assertContains('Ovino', $especies);
    }

    public function test_calcula_total_de_animais_por_especie(): void
    {
        Rebanho::factory()->create(['especie' => 'Bovino', 'quantidade' => 100]);
        Rebanho::factory()->create(['especie' => 'Bovino', 'quantidade' => 150]);
        Rebanho::factory()->create(['especie' => 'Caprino', 'quantidade' => 80]);

        $response = $this->getJson('/api/relatorios/animais-por-especie', $this->authHeaders());

        $response->assertStatus(200);

        $bovino = collect($response->json('data'))->firstWhere('especie', 'Bovino');
        $caprino = collect($response->json('data'))->firstWhere('especie', 'Caprino');

        $this->assertEquals(250, $bovino['total_animais']);
        $this->assertEquals(80, $caprino['total_animais']);
    }

    public function test_calcula_total_de_rebanhos_por_especie(): void
    {
        Rebanho::factory()->count(5)->create(['especie' => 'Bovino']);
        Rebanho::factory()->count(3)->create(['especie' => 'Caprino']);

        $response = $this->getJson('/api/relatorios/animais-por-especie', $this->authHeaders());

        $response->assertStatus(200);

        $bovino = collect($response->json('data'))->firstWhere('especie', 'Bovino');
        $caprino = collect($response->json('data'))->firstWhere('especie', 'Caprino');

        $this->assertEquals(5, $bovino['total_rebanhos']);
        $this->assertEquals(3, $caprino['total_rebanhos']);
    }

    public function test_conta_propriedades_unicas_por_especie(): void
    {
        $propriedade1 = Propriedade::factory()->create();
        $propriedade2 = Propriedade::factory()->create();
        $propriedade3 = Propriedade::factory()->create();

        // 3 rebanhos de Bovino em 2 propriedades únicas
        Rebanho::factory()->create(['especie' => 'Bovino', 'propriedade_id' => $propriedade1->id]);
        Rebanho::factory()->create(['especie' => 'Bovino', 'propriedade_id' => $propriedade1->id]);
        Rebanho::factory()->create(['especie' => 'Bovino', 'propriedade_id' => $propriedade2->id]);

        // 2 rebanhos de Caprino em 2 propriedades únicas
        Rebanho::factory()->create(['especie' => 'Caprino', 'propriedade_id' => $propriedade2->id]);
        Rebanho::factory()->create(['especie' => 'Caprino', 'propriedade_id' => $propriedade3->id]);

        $response = $this->getJson('/api/relatorios/animais-por-especie', $this->authHeaders());

        $response->assertStatus(200);

        $bovino = collect($response->json('data'))->firstWhere('especie', 'Bovino');
        $caprino = collect($response->json('data'))->firstWhere('especie', 'Caprino');

        $this->assertEquals(2, $bovino['total_propriedades']);
        $this->assertEquals(2, $caprino['total_propriedades']);
    }

    public function test_calcula_media_de_animais_por_rebanho(): void
    {
        Rebanho::factory()->create(['especie' => 'Bovino', 'quantidade' => 100]);
        Rebanho::factory()->create(['especie' => 'Bovino', 'quantidade' => 200]);
        Rebanho::factory()->create(['especie' => 'Bovino', 'quantidade' => 300]);

        $response = $this->getJson('/api/relatorios/animais-por-especie', $this->authHeaders());

        $response->assertStatus(200);

        $bovino = collect($response->json('data'))->firstWhere('especie', 'Bovino');

        $this->assertEquals(200.0, $bovino['media_animais_por_rebanho']);
    }

    public function test_ordena_especies_alfabeticamente(): void
    {
        Rebanho::factory()->create(['especie' => 'Suíno']);
        Rebanho::factory()->create(['especie' => 'Bovino']);
        Rebanho::factory()->create(['especie' => 'Caprino']);

        $response = $this->getJson('/api/relatorios/animais-por-especie', $this->authHeaders());

        $response->assertStatus(200);

        $especies = collect($response->json('data'))->pluck('especie')->toArray();

        $this->assertEquals(['Bovino', 'Caprino', 'Suíno'], $especies);
    }

    public function test_pode_filtrar_por_especie_especifica(): void
    {
        Rebanho::factory()->count(3)->create(['especie' => 'Bovino', 'quantidade' => 100]);
        Rebanho::factory()->count(2)->create(['especie' => 'Caprino', 'quantidade' => 50]);

        $response = $this->getJson('/api/relatorios/animais-por-especie?especie=Bovino', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

        $resultado = $response->json('data.0');
        $this->assertEquals('Bovino', $resultado['especie']);
        $this->assertEquals(300, $resultado['total_animais']);
        $this->assertEquals(3, $resultado['total_rebanhos']);
    }

    public function test_retorna_array_vazio_quando_nao_ha_dados(): void
    {
        $response = $this->getJson('/api/relatorios/animais-por-especie', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    public function test_estrutura_json_do_relatorio_esta_correta(): void
    {
        Rebanho::factory()->create([
            'especie' => 'Bovino',
            'quantidade' => 150,
        ]);

        $response = $this->getJson('/api/relatorios/animais-por-especie', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'especie',
                    'total_animais',
                    'total_rebanhos',
                    'total_propriedades',
                    'media_animais_por_rebanho',
                ],
            ],
        ]);

        $resultado = $response->json('data.0');

        $this->assertIsString($resultado['especie']);
        $this->assertIsInt($resultado['total_animais']);
        $this->assertIsInt($resultado['total_rebanhos']);
        $this->assertIsInt($resultado['total_propriedades']);
        $this->assertIsNumeric($resultado['media_animais_por_rebanho']);
    }
}
