<?php

namespace Tests\Unit\Models;

use App\Models\ProdutorRural;
use App\Models\Propriedade;
use App\Models\UnidadeProducao;
use App\Models\Rebanho;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropriedadeTest extends TestCase
{
    use RefreshDatabase;

    public function test_pode_criar_propriedade(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'José Silva',
            'cpf_cnpj' => '123.456.789-00',
        ]);

        $propriedade = Propriedade::create([
            'nome' => 'Fazenda Santa Maria',
            'municipio' => 'Fortaleza',
            'uf' => 'CE',
            'inscricao_estadual' => 'IE123456',
            'area_total' => 150.75,
            'produtor_id' => $produtor->id,
        ]);

        $this->assertInstanceOf(Propriedade::class, $propriedade);
        $this->assertEquals('Fazenda Santa Maria', $propriedade->nome);
        $this->assertEquals('Fortaleza', $propriedade->municipio);
        $this->assertEquals(150.75, $propriedade->area_total);
        $this->assertDatabaseHas('propriedades', [
            'nome' => 'Fazenda Santa Maria',
            'municipio' => 'Fortaleza',
        ]);
    }

    public function test_campos_sao_fillable(): void
    {
        $propriedade = new Propriedade();

        $fillable = [
            'nome',
            'municipio',
            'uf',
            'inscricao_estadual',
            'area_total',
            'produtor_id',
        ];

        $this->assertEquals($fillable, $propriedade->getFillable());
    }

    public function test_area_total_e_cast_para_decimal(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'Maria Santos',
            'cpf_cnpj' => '987.654.321-00',
        ]);

        $propriedade = Propriedade::create([
            'nome' => 'Sítio Alegre',
            'municipio' => 'Maracanaú',
            'uf' => 'CE',
            'area_total' => '250.50',
            'produtor_id' => $produtor->id,
        ]);

        $this->assertEquals('250.50', $propriedade->area_total);
    }

    public function test_inscricao_estadual_deve_ser_unica(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'João Costa',
            'cpf_cnpj' => '111.222.333-44',
        ]);

        Propriedade::create([
            'nome' => 'Propriedade 1',
            'municipio' => 'Caucaia',
            'uf' => 'CE',
            'inscricao_estadual' => 'IE999888',
            'area_total' => 100,
            'produtor_id' => $produtor->id,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Propriedade::create([
            'nome' => 'Propriedade 2',
            'municipio' => 'Caucaia',
            'uf' => 'CE',
            'inscricao_estadual' => 'IE999888',
            'area_total' => 200,
            'produtor_id' => $produtor->id,
        ]);
    }

    public function test_tem_relacionamento_belongs_to_com_produtor_rural(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'Pedro Oliveira',
            'cpf_cnpj' => '555.666.777-88',
        ]);

        $propriedade = Propriedade::create([
            'nome' => 'Fazenda Boa Vista',
            'municipio' => 'Aquiraz',
            'uf' => 'CE',
            'area_total' => 300.00,
            'produtor_id' => $produtor->id,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $propriedade->produtor());
        $this->assertInstanceOf(ProdutorRural::class, $propriedade->produtor);
        $this->assertEquals($produtor->id, $propriedade->produtor->id);
        $this->assertEquals('Pedro Oliveira', $propriedade->produtor->nome);
    }

    public function test_tem_relacionamento_has_many_com_unidades_producao(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'Ana Paula',
            'cpf_cnpj' => '444.555.666-77',
        ]);

        $propriedade = Propriedade::create([
            'nome' => 'Chácara Verde',
            'municipio' => 'Eusébio',
            'uf' => 'CE',
            'area_total' => 50.00,
            'produtor_id' => $produtor->id,
        ]);

        $unidade1 = UnidadeProducao::create([
            'nome_cultura' => 'Milho',
            'area_total_ha' => 20.00,
            'propriedade_id' => $propriedade->id,
        ]);

        $unidade2 = UnidadeProducao::create([
            'nome_cultura' => 'Feijão',
            'area_total_ha' => 15.00,
            'propriedade_id' => $propriedade->id,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $propriedade->unidadesProducao());
        $this->assertCount(2, $propriedade->unidadesProducao);
        $this->assertTrue($propriedade->unidadesProducao->contains($unidade1));
        $this->assertTrue($propriedade->unidadesProducao->contains($unidade2));
    }

    public function test_tem_relacionamento_has_many_com_rebanhos(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'Carlos Souza',
            'cpf_cnpj' => '222.333.444-55',
        ]);

        $propriedade = Propriedade::create([
            'nome' => 'Fazenda Lagoa Azul',
            'municipio' => 'Maranguape',
            'uf' => 'CE',
            'area_total' => 400.00,
            'produtor_id' => $produtor->id,
        ]);

        $rebanho1 = Rebanho::create([
            'especie' => 'Bovino',
            'quantidade' => 50,
            'finalidade' => 'Corte',
            'propriedade_id' => $propriedade->id,
        ]);

        $rebanho2 = Rebanho::create([
            'especie' => 'Caprino',
            'quantidade' => 30,
            'finalidade' => 'Leite',
            'propriedade_id' => $propriedade->id,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $propriedade->rebanhos());
        $this->assertCount(2, $propriedade->rebanhos);
        $this->assertTrue($propriedade->rebanhos->contains($rebanho1));
        $this->assertTrue($propriedade->rebanhos->contains($rebanho2));
    }

    public function test_usa_tabela_propriedades(): void
    {
        $propriedade = new Propriedade();
        $this->assertEquals('propriedades', $propriedade->getTable());
    }

    public function test_inscricao_estadual_pode_ser_null(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'Lucas Lima',
            'cpf_cnpj' => '777.888.999-00',
        ]);

        $propriedade = Propriedade::create([
            'nome' => 'Roça do Zé',
            'municipio' => 'Pacatuba',
            'uf' => 'CE',
            'inscricao_estadual' => null,
            'area_total' => 10.50,
            'produtor_id' => $produtor->id,
        ]);

        $this->assertNull($propriedade->inscricao_estadual);
        $this->assertDatabaseHas('propriedades', [
            'nome' => 'Roça do Zé',
            'inscricao_estadual' => null,
        ]);
    }
}
