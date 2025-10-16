<?php

namespace Tests\Feature\Api;

use App\Models\Propriedade;
use App\Models\Rebanho;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RebanhoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_pode_listar_todos_os_rebanhos(): void
    {
        Rebanho::factory()->count(3)->create();

        $response = $this->getJson('/api/rebanhos');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'especie',
                        'quantidade',
                        'finalidade',
                        'data_atualizacao',
                        'propriedade_id',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_pode_criar_rebanho_com_dados_validos(): void
    {
        $propriedade = Propriedade::factory()->create();

        $dados = [
            'especie' => 'Bovino',
            'quantidade' => 150,
            'finalidade' => 'Corte',
            'data_atualizacao' => '2025-10-15 10:00:00',
            'propriedade_id' => $propriedade->id,
        ];

        $response = $this->postJson('/api/rebanhos', $dados);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'especie' => 'Bovino',
                    'quantidade' => 150,
                    'finalidade' => 'Corte',
                ],
            ]);

        $this->assertDatabaseHas('rebanhos', [
            'especie' => 'Bovino',
            'quantidade' => 150,
            'finalidade' => 'Corte',
        ]);
    }

    public function test_nao_pode_criar_rebanho_sem_especie(): void
    {
        $propriedade = Propriedade::factory()->create();

        $dados = [
            'quantidade' => 100,
            'propriedade_id' => $propriedade->id,
        ];

        $response = $this->postJson('/api/rebanhos', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['especie']);
    }

    public function test_nao_pode_criar_rebanho_sem_quantidade(): void
    {
        $propriedade = Propriedade::factory()->create();

        $dados = [
            'especie' => 'Caprino',
            'propriedade_id' => $propriedade->id,
        ];

        $response = $this->postJson('/api/rebanhos', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantidade']);
    }

    public function test_nao_pode_criar_rebanho_com_quantidade_zero(): void
    {
        $propriedade = Propriedade::factory()->create();

        $dados = [
            'especie' => 'Ovino',
            'quantidade' => 0,
            'propriedade_id' => $propriedade->id,
        ];

        $response = $this->postJson('/api/rebanhos', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantidade']);
    }

    public function test_nao_pode_criar_rebanho_com_quantidade_negativa(): void
    {
        $propriedade = Propriedade::factory()->create();

        $dados = [
            'especie' => 'Suíno',
            'quantidade' => -10,
            'propriedade_id' => $propriedade->id,
        ];

        $response = $this->postJson('/api/rebanhos', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantidade']);
    }

    public function test_nao_pode_criar_rebanho_sem_propriedade_id(): void
    {
        $dados = [
            'especie' => 'Aves',
            'quantidade' => 1000,
        ];

        $response = $this->postJson('/api/rebanhos', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['propriedade_id']);
    }

    public function test_nao_pode_criar_rebanho_com_propriedade_inexistente(): void
    {
        $dados = [
            'especie' => 'Aves',
            'quantidade' => 1000,
            'propriedade_id' => 999,
        ];

        $response = $this->postJson('/api/rebanhos', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['propriedade_id']);
    }

    public function test_finalidade_pode_ser_null(): void
    {
        $propriedade = Propriedade::factory()->create();

        $dados = [
            'especie' => 'Equino',
            'quantidade' => 15,
            'propriedade_id' => $propriedade->id,
        ];

        $response = $this->postJson('/api/rebanhos', $dados);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'especie' => 'Equino',
                    'finalidade' => null,
                ],
            ]);
    }

    public function test_data_atualizacao_pode_ser_null(): void
    {
        $propriedade = Propriedade::factory()->create();

        $dados = [
            'especie' => 'Bubalino',
            'quantidade' => 50,
            'finalidade' => 'Leite',
            'propriedade_id' => $propriedade->id,
        ];

        $response = $this->postJson('/api/rebanhos', $dados);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'especie' => 'Bubalino',
                    'data_atualizacao' => null,
                ],
            ]);
    }

    public function test_pode_mostrar_um_rebanho_especifico(): void
    {
        $rebanho = Rebanho::factory()->create([
            'especie' => 'Ovino',
            'quantidade' => 200,
            'finalidade' => 'Lã',
        ]);

        $response = $this->getJson("/api/rebanhos/{$rebanho->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $rebanho->id,
                    'especie' => 'Ovino',
                    'quantidade' => 200,
                    'finalidade' => 'Lã',
                ],
            ]);
    }

    public function test_retorna_404_ao_buscar_rebanho_inexistente(): void
    {
        $response = $this->getJson('/api/rebanhos/999');

        $response->assertStatus(404);
    }

    public function test_pode_atualizar_rebanho(): void
    {
        $rebanho = Rebanho::factory()->create([
            'especie' => 'Bovino',
            'quantidade' => 100,
            'finalidade' => 'Corte',
        ]);

        $dadosAtualizados = [
            'especie' => 'Bovino',
            'quantidade' => 125,
            'finalidade' => 'Misto',
            'data_atualizacao' => '2025-10-16 15:30:00',
            'propriedade_id' => $rebanho->propriedade_id,
        ];

        $response = $this->putJson("/api/rebanhos/{$rebanho->id}", $dadosAtualizados);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'quantidade' => 125,
                    'finalidade' => 'Misto',
                ],
            ]);

        $this->assertDatabaseHas('rebanhos', [
            'id' => $rebanho->id,
            'quantidade' => 125,
            'finalidade' => 'Misto',
        ]);
    }

    public function test_pode_deletar_rebanho(): void
    {
        $rebanho = Rebanho::factory()->create();

        $response = $this->deleteJson("/api/rebanhos/{$rebanho->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('rebanhos', [
            'id' => $rebanho->id,
        ]);
    }

    public function test_retorna_404_ao_deletar_rebanho_inexistente(): void
    {
        $response = $this->deleteJson('/api/rebanhos/999');

        $response->assertStatus(404);
    }
}
