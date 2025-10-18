<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\User;

use App\Models\ProdutorRural;
use App\Models\Propriedade;
use App\Models\UnidadeProducao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnidadeProducaoControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaders(UserRole $role = UserRole::GESTOR): array
    {
        $user = User::factory()->create(['role' => $role]);
        $token = $user->createToken('test')->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_pode_listar_todas_as_unidades_producao(): void
    {
        UnidadeProducao::factory()->count(3)->create();

        $response = $this->getJson('/api/unidades-producao', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'nome_cultura',
                        'area_total_ha',
                        'coordenadas_geograficas',
                        'propriedade_id',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_pode_criar_unidade_producao_com_dados_validos(): void
    {
        $propriedade = Propriedade::factory()->create();

        $dados = [
            'nome_cultura' => 'Milho',
            'area_total_ha' => 120.50,
            'coordenadas_geograficas' => '-3.6889, -40.3441',
            'propriedade_id' => $propriedade->id,
        ];

        $response = $this->postJson('/api/unidades-producao', $dados, $this->authHeaders());

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'nome_cultura' => 'Milho',
                    'area_total_ha' => '120.50',
                    'coordenadas_geograficas' => '-3.6889, -40.3441',
                ],
            ]);

        $this->assertDatabaseHas('unidades_producao', [
            'nome_cultura' => 'Milho',
            'area_total_ha' => 120.50,
        ]);
    }

    public function test_nao_pode_criar_unidade_producao_sem_nome_cultura(): void
    {
        $propriedade = Propriedade::factory()->create();

        $dados = [
            'area_total_ha' => 100.00,
            'propriedade_id' => $propriedade->id,
        ];

        $response = $this->postJson('/api/unidades-producao', $dados, $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome_cultura']);
    }

    public function test_nao_pode_criar_unidade_producao_sem_area_total_ha(): void
    {
        $propriedade = Propriedade::factory()->create();

        $dados = [
            'nome_cultura' => 'Feijão',
            'propriedade_id' => $propriedade->id,
        ];

        $response = $this->postJson('/api/unidades-producao', $dados, $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['area_total_ha']);
    }

    public function test_nao_pode_criar_unidade_producao_com_area_total_ha_negativa(): void
    {
        $propriedade = Propriedade::factory()->create();

        $dados = [
            'nome_cultura' => 'Soja',
            'area_total_ha' => -10,
            'propriedade_id' => $propriedade->id,
        ];

        $response = $this->postJson('/api/unidades-producao', $dados, $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['area_total_ha']);
    }

    public function test_nao_pode_criar_unidade_producao_sem_propriedade_id(): void
    {
        $dados = [
            'nome_cultura' => 'Algodão',
            'area_total_ha' => 50.00,
        ];

        $response = $this->postJson('/api/unidades-producao', $dados, $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['propriedade_id']);
    }

    public function test_nao_pode_criar_unidade_producao_com_propriedade_inexistente(): void
    {
        $dados = [
            'nome_cultura' => 'Algodão',
            'area_total_ha' => 50.00,
            'propriedade_id' => 999,
        ];

        $response = $this->postJson('/api/unidades-producao', $dados, $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['propriedade_id']);
    }

    public function test_coordenadas_geograficas_pode_ser_null(): void
    {
        $propriedade = Propriedade::factory()->create();

        $dados = [
            'nome_cultura' => 'Mandioca',
            'area_total_ha' => 25.75,
            'propriedade_id' => $propriedade->id,
        ];

        $response = $this->postJson('/api/unidades-producao', $dados, $this->authHeaders());

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'nome_cultura' => 'Mandioca',
                    'coordenadas_geograficas' => null,
                ],
            ]);
    }

    public function test_pode_mostrar_uma_unidade_producao_especifica(): void
    {
        $unidade = UnidadeProducao::factory()->create([
            'nome_cultura' => 'Cana-de-açúcar',
            'area_total_ha' => 200.00,
        ]);

        $response = $this->getJson("/api/unidades-producao/{$unidade->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $unidade->id,
                    'nome_cultura' => 'Cana-de-açúcar',
                    'area_total_ha' => '200.00',
                ],
            ]);
    }

    public function test_retorna_404_ao_buscar_unidade_producao_inexistente(): void
    {
        $response = $this->getJson('/api/unidades-producao/999', $this->authHeaders());

        $response->assertStatus(404);
    }

    public function test_pode_atualizar_unidade_producao(): void
    {
        $unidade = UnidadeProducao::factory()->create([
            'nome_cultura' => 'Milho',
            'area_total_ha' => 100.00,
        ]);

        $dadosAtualizados = [
            'nome_cultura' => 'Milho Híbrido',
            'area_total_ha' => 150.75,
            'coordenadas_geograficas' => '-3.7500, -40.5000',
            'propriedade_id' => $unidade->propriedade_id,
        ];

        $response = $this->putJson("/api/unidades-producao/{$unidade->id}", $dadosAtualizados, $this->authHeaders());

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'nome_cultura' => 'Milho Híbrido',
                    'area_total_ha' => '150.75',
                    'coordenadas_geograficas' => '-3.7500, -40.5000',
                ],
            ]);

        $this->assertDatabaseHas('unidades_producao', [
            'id' => $unidade->id,
            'nome_cultura' => 'Milho Híbrido',
            'area_total_ha' => 150.75,
        ]);
    }

    public function test_pode_deletar_unidade_producao(): void
    {
        $unidade = UnidadeProducao::factory()->create();

        $response = $this->deleteJson("/api/unidades-producao/{$unidade->id}", [], $this->authHeaders(UserRole::ADMIN));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('unidades_producao', [
            'id' => $unidade->id,
        ]);
    }

    public function test_retorna_404_ao_deletar_unidade_producao_inexistente(): void
    {
        $response = $this->deleteJson('/api/unidades-producao/999', [], $this->authHeaders(UserRole::ADMIN));

        $response->assertStatus(404);
    }
}
