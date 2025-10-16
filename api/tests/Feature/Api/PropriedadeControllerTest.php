<?php

namespace Tests\Feature\Api;

use App\Models\ProdutorRural;
use App\Models\Propriedade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropriedadeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_pode_listar_todas_as_propriedades(): void
    {
        Propriedade::factory()->count(3)->create();

        $response = $this->getJson('/api/propriedades');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'nome',
                        'municipio',
                        'uf',
                        'inscricao_estadual',
                        'area_total',
                        'produtor_id',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_pode_criar_propriedade_com_dados_validos(): void
    {
        $produtor = ProdutorRural::factory()->create();

        $dados = [
            'nome' => 'Fazenda Boa Vista',
            'municipio' => 'Fortaleza',
            'uf' => 'CE',
            'inscricao_estadual' => 'IE-12345678',
            'area_total' => 150.50,
            'produtor_id' => $produtor->id,
        ];

        $response = $this->postJson('/api/propriedades', $dados);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'nome' => 'Fazenda Boa Vista',
                    'municipio' => 'Fortaleza',
                    'uf' => 'CE',
                    'area_total' => '150.50',
                ],
            ]);

        $this->assertDatabaseHas('propriedades', [
            'nome' => 'Fazenda Boa Vista',
            'municipio' => 'Fortaleza',
        ]);
    }

    public function test_nao_pode_criar_propriedade_sem_nome(): void
    {
        $produtor = ProdutorRural::factory()->create();

        $dados = [
            'municipio' => 'Fortaleza',
            'uf' => 'CE',
            'area_total' => 100,
            'produtor_id' => $produtor->id,
        ];

        $response = $this->postJson('/api/propriedades', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome']);
    }

    public function test_nao_pode_criar_propriedade_sem_produtor_id(): void
    {
        $dados = [
            'nome' => 'Fazenda Teste',
            'municipio' => 'Fortaleza',
            'uf' => 'CE',
            'area_total' => 100,
        ];

        $response = $this->postJson('/api/propriedades', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['produtor_id']);
    }

    public function test_nao_pode_criar_propriedade_com_produtor_inexistente(): void
    {
        $dados = [
            'nome' => 'Fazenda Teste',
            'municipio' => 'Fortaleza',
            'uf' => 'CE',
            'area_total' => 100,
            'produtor_id' => 999,
        ];

        $response = $this->postJson('/api/propriedades', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['produtor_id']);
    }

    public function test_nao_pode_criar_propriedade_com_inscricao_estadual_duplicada(): void
    {
        $produtor = ProdutorRural::factory()->create();

        Propriedade::create([
            'nome' => 'Propriedade 1',
            'municipio' => 'Caucaia',
            'uf' => 'CE',
            'inscricao_estadual' => 'IE-99988877',
            'area_total' => 100,
            'produtor_id' => $produtor->id,
        ]);

        $dados = [
            'nome' => 'Propriedade 2',
            'municipio' => 'Caucaia',
            'uf' => 'CE',
            'inscricao_estadual' => 'IE-99988877',
            'area_total' => 200,
            'produtor_id' => $produtor->id,
        ];

        $response = $this->postJson('/api/propriedades', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['inscricao_estadual']);
    }

    public function test_pode_mostrar_uma_propriedade_especifica(): void
    {
        $propriedade = Propriedade::factory()->create([
            'nome' => 'Fazenda São José',
            'municipio' => 'Sobral',
        ]);

        $response = $this->getJson("/api/propriedades/{$propriedade->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $propriedade->id,
                    'nome' => 'Fazenda São José',
                    'municipio' => 'Sobral',
                ],
            ]);
    }

    public function test_retorna_404_ao_buscar_propriedade_inexistente(): void
    {
        $response = $this->getJson('/api/propriedades/999');

        $response->assertStatus(404);
    }

    public function test_pode_atualizar_propriedade(): void
    {
        $propriedade = Propriedade::factory()->create([
            'nome' => 'Fazenda Original',
            'area_total' => 100.00,
        ]);

        $dadosAtualizados = [
            'nome' => 'Fazenda Atualizada',
            'municipio' => $propriedade->municipio,
            'uf' => $propriedade->uf,
            'area_total' => 250.75,
            'produtor_id' => $propriedade->produtor_id,
        ];

        $response = $this->putJson("/api/propriedades/{$propriedade->id}", $dadosAtualizados);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'nome' => 'Fazenda Atualizada',
                    'area_total' => '250.75',
                ],
            ]);

        $this->assertDatabaseHas('propriedades', [
            'id' => $propriedade->id,
            'nome' => 'Fazenda Atualizada',
            'area_total' => 250.75,
        ]);
    }

    public function test_nao_pode_atualizar_com_inscricao_estadual_de_outra_propriedade(): void
    {
        $produtor = ProdutorRural::factory()->create();

        $propriedade1 = Propriedade::create([
            'nome' => 'Propriedade 1',
            'municipio' => 'Fortaleza',
            'uf' => 'CE',
            'inscricao_estadual' => 'IE-111111',
            'area_total' => 100,
            'produtor_id' => $produtor->id,
        ]);

        $propriedade2 = Propriedade::create([
            'nome' => 'Propriedade 2',
            'municipio' => 'Fortaleza',
            'uf' => 'CE',
            'inscricao_estadual' => 'IE-222222',
            'area_total' => 200,
            'produtor_id' => $produtor->id,
        ]);

        $dadosAtualizados = [
            'nome' => 'Propriedade 2 Atualizada',
            'municipio' => 'Fortaleza',
            'uf' => 'CE',
            'inscricao_estadual' => 'IE-111111',
            'area_total' => 200,
            'produtor_id' => $produtor->id,
        ];

        $response = $this->putJson("/api/propriedades/{$propriedade2->id}", $dadosAtualizados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['inscricao_estadual']);
    }

    public function test_pode_deletar_propriedade(): void
    {
        $propriedade = Propriedade::factory()->create();

        $response = $this->deleteJson("/api/propriedades/{$propriedade->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('propriedades', [
            'id' => $propriedade->id,
        ]);
    }

    public function test_retorna_404_ao_deletar_propriedade_inexistente(): void
    {
        $response = $this->deleteJson('/api/propriedades/999');

        $response->assertStatus(404);
    }

    public function test_uf_deve_ter_2_caracteres(): void
    {
        $produtor = ProdutorRural::factory()->create();

        $dados = [
            'nome' => 'Fazenda Teste',
            'municipio' => 'Fortaleza',
            'uf' => 'CEA',
            'area_total' => 100,
            'produtor_id' => $produtor->id,
        ];

        $response = $this->postJson('/api/propriedades', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['uf']);
    }

    public function test_inscricao_estadual_pode_ser_null(): void
    {
        $produtor = ProdutorRural::factory()->create();

        $dados = [
            'nome' => 'Sítio Pequeno',
            'municipio' => 'Pacatuba',
            'uf' => 'CE',
            'area_total' => 10.50,
            'produtor_id' => $produtor->id,
        ];

        $response = $this->postJson('/api/propriedades', $dados);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'nome' => 'Sítio Pequeno',
                    'inscricao_estadual' => null,
                ],
            ]);
    }
}
