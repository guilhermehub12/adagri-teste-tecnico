<?php

namespace Tests\Feature\Api;

use App\Models\ProdutorRural;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProdutorRuralControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_pode_listar_todos_os_produtores_rurais(): void
    {
        ProdutorRural::factory()->count(3)->create();

        $response = $this->getJson('/api/produtores-rurais');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'nome',
                        'cpf_cnpj',
                        'telefone',
                        'email',
                        'endereco',
                        'data_cadastro',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_pode_criar_produtor_rural_com_dados_validos(): void
    {
        $dados = [
            'nome' => 'João da Silva',
            'cpf_cnpj' => '123.456.789-00',
            'telefone' => '(85) 98765-4321',
            'email' => 'joao@example.com',
            'endereco' => 'Rua das Flores, 123',
        ];

        $response = $this->postJson('/api/produtores-rurais', $dados);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'nome' => 'João da Silva',
                    'cpf_cnpj' => '123.456.789-00',
                    'email' => 'joao@example.com',
                ],
            ]);

        $this->assertDatabaseHas('produtores_rurais', [
            'nome' => 'João da Silva',
            'cpf_cnpj' => '123.456.789-00',
        ]);
    }

    public function test_nao_pode_criar_produtor_sem_nome(): void
    {
        $dados = [
            'cpf_cnpj' => '123.456.789-00',
        ];

        $response = $this->postJson('/api/produtores-rurais', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome']);
    }

    public function test_nao_pode_criar_produtor_sem_cpf_cnpj(): void
    {
        $dados = [
            'nome' => 'Maria Santos',
        ];

        $response = $this->postJson('/api/produtores-rurais', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cpf_cnpj']);
    }

    public function test_nao_pode_criar_produtor_com_cpf_cnpj_duplicado(): void
    {
        ProdutorRural::create([
            'nome' => 'Produtor 1',
            'cpf_cnpj' => '111.222.333-44',
        ]);

        $dados = [
            'nome' => 'Produtor 2',
            'cpf_cnpj' => '111.222.333-44',
        ];

        $response = $this->postJson('/api/produtores-rurais', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cpf_cnpj']);
    }

    public function test_pode_mostrar_um_produtor_rural_especifico(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'José Oliveira',
            'cpf_cnpj' => '555.666.777-88',
            'email' => 'jose@example.com',
        ]);

        $response = $this->getJson("/api/produtores-rurais/{$produtor->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $produtor->id,
                    'nome' => 'José Oliveira',
                    'cpf_cnpj' => '555.666.777-88',
                    'email' => 'jose@example.com',
                ],
            ]);
    }

    public function test_retorna_404_ao_buscar_produtor_inexistente(): void
    {
        $response = $this->getJson('/api/produtores-rurais/999');

        $response->assertStatus(404);
    }

    public function test_pode_atualizar_produtor_rural(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'Pedro Souza',
            'cpf_cnpj' => '999.888.777-66',
            'email' => 'pedro@example.com',
        ]);

        $dadosAtualizados = [
            'nome' => 'Pedro Souza Atualizado',
            'cpf_cnpj' => '999.888.777-66',
            'email' => 'pedro.novo@example.com',
            'telefone' => '(85) 91111-2222',
        ];

        $response = $this->putJson("/api/produtores-rurais/{$produtor->id}", $dadosAtualizados);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'nome' => 'Pedro Souza Atualizado',
                    'email' => 'pedro.novo@example.com',
                    'telefone' => '(85) 91111-2222',
                ],
            ]);

        $this->assertDatabaseHas('produtores_rurais', [
            'id' => $produtor->id,
            'nome' => 'Pedro Souza Atualizado',
            'email' => 'pedro.novo@example.com',
        ]);
    }

    public function test_nao_pode_atualizar_com_cpf_cnpj_de_outro_produtor(): void
    {
        $produtor1 = ProdutorRural::create([
            'nome' => 'Produtor 1',
            'cpf_cnpj' => '111.111.111-11',
        ]);

        $produtor2 = ProdutorRural::create([
            'nome' => 'Produtor 2',
            'cpf_cnpj' => '222.222.222-22',
        ]);

        $dadosAtualizados = [
            'nome' => 'Produtor 2 Atualizado',
            'cpf_cnpj' => '111.111.111-11',
        ];

        $response = $this->putJson("/api/produtores-rurais/{$produtor2->id}", $dadosAtualizados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cpf_cnpj']);
    }

    public function test_pode_deletar_produtor_rural(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'Ana Paula',
            'cpf_cnpj' => '444.555.666-77',
        ]);

        $response = $this->deleteJson("/api/produtores-rurais/{$produtor->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('produtores_rurais', [
            'id' => $produtor->id,
        ]);
    }

    public function test_retorna_404_ao_deletar_produtor_inexistente(): void
    {
        $response = $this->deleteJson('/api/produtores-rurais/999');

        $response->assertStatus(404);
    }

    public function test_email_deve_ser_valido(): void
    {
        $dados = [
            'nome' => 'Carlos Silva',
            'cpf_cnpj' => '777.888.999-00',
            'email' => 'email-invalido',
        ];

        $response = $this->postJson('/api/produtores-rurais', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_campos_opcionais_podem_ser_nulos(): void
    {
        $dados = [
            'nome' => 'Lucas Lima',
            'cpf_cnpj' => '333.444.555-66',
        ];

        $response = $this->postJson('/api/produtores-rurais', $dados);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'nome' => 'Lucas Lima',
                    'telefone' => null,
                    'email' => null,
                    'endereco' => null,
                ],
            ]);
    }
}
