<?php

namespace Tests\Unit\Models;

use App\Models\ProdutorRural;
use App\Models\Propriedade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProdutorRuralTest extends TestCase
{
    use RefreshDatabase;

    public function test_pode_criar_produtor_rural(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'João da Silva',
            'cpf_cnpj' => '123.456.789-00',
            'telefone' => '(85) 98765-4321',
            'email' => 'joao@example.com',
            'endereco' => 'Rua das Flores, 123',
        ]);

        $this->assertInstanceOf(ProdutorRural::class, $produtor);
        $this->assertEquals('João da Silva', $produtor->nome);
        $this->assertEquals('123.456.789-00', $produtor->cpf_cnpj);
        $this->assertDatabaseHas('produtores_rurais', [
            'nome' => 'João da Silva',
            'cpf_cnpj' => '123.456.789-00',
        ]);
    }

    public function test_campos_sao_fillable(): void
    {
        $produtor = new ProdutorRural();

        $fillable = [
            'nome',
            'cpf_cnpj',
            'telefone',
            'email',
            'endereco',
            'data_cadastro',
        ];

        $this->assertEquals($fillable, $produtor->getFillable());
    }

    public function test_data_cadastro_e_cast_para_datetime(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'Maria Santos',
            'cpf_cnpj' => '12.345.678/0001-90',
            'data_cadastro' => '2025-10-15 10:30:00',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $produtor->data_cadastro);
    }

    public function test_cpf_cnpj_deve_ser_unico(): void
    {
        ProdutorRural::create([
            'nome' => 'Produtor 1',
            'cpf_cnpj' => '111.222.333-44',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        ProdutorRural::create([
            'nome' => 'Produtor 2',
            'cpf_cnpj' => '111.222.333-44',
        ]);
    }

    public function test_tem_relacionamento_has_many_com_propriedades(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'José Oliveira',
            'cpf_cnpj' => '555.666.777-88',
        ]);

        $propriedade1 = Propriedade::create([
            'nome' => 'Fazenda Boa Vista',
            'municipio' => 'Fortaleza',
            'uf' => 'CE',
            'area_total' => 100.50,
            'produtor_id' => $produtor->id,
        ]);

        $propriedade2 = Propriedade::create([
            'nome' => 'Sítio São José',
            'municipio' => 'Fortaleza',
            'uf' => 'CE',
            'area_total' => 50.00,
            'produtor_id' => $produtor->id,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $produtor->propriedades());
        $this->assertCount(2, $produtor->propriedades);
        $this->assertTrue($produtor->propriedades->contains($propriedade1));
        $this->assertTrue($produtor->propriedades->contains($propriedade2));
    }

    public function test_usa_tabela_produtores_rurais(): void
    {
        $produtor = new ProdutorRural();
        $this->assertEquals('produtores_rurais', $produtor->getTable());
    }

    public function test_campos_opcionais_podem_ser_null(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'Pedro Souza',
            'cpf_cnpj' => '999.888.777-66',
            'telefone' => null,
            'email' => null,
            'endereco' => null,
        ]);

        $this->assertNull($produtor->telefone);
        $this->assertNull($produtor->email);
        $this->assertNull($produtor->endereco);
        $this->assertDatabaseHas('produtores_rurais', [
            'nome' => 'Pedro Souza',
            'telefone' => null,
            'email' => null,
            'endereco' => null,
        ]);
    }
}
