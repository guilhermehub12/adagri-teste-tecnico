<?php

namespace Tests\Unit\Models;

use App\Models\ProdutorRural;
use App\Models\Propriedade;
use App\Models\UnidadeProducao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnidadeProducaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_pode_criar_unidade_producao(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'João Fazendeiro',
            'cpf_cnpj' => '111.222.333-44',
        ]);

        $propriedade = Propriedade::create([
            'nome' => 'Fazenda Progresso',
            'municipio' => 'Sobral',
            'uf' => 'CE',
            'area_total' => 500.00,
            'produtor_id' => $produtor->id,
        ]);

        $unidade = UnidadeProducao::create([
            'nome_cultura' => 'Milho',
            'area_total_ha' => 120.50,
            'coordenadas_geograficas' => '-3.6889, -40.3441',
            'propriedade_id' => $propriedade->id,
        ]);

        $this->assertInstanceOf(UnidadeProducao::class, $unidade);
        $this->assertEquals('Milho', $unidade->nome_cultura);
        $this->assertEquals(120.50, $unidade->area_total_ha);
        $this->assertEquals('-3.6889, -40.3441', $unidade->coordenadas_geograficas);
        $this->assertDatabaseHas('unidades_producao', [
            'nome_cultura' => 'Milho',
            'area_total_ha' => 120.50,
        ]);
    }

    public function test_campos_sao_fillable(): void
    {
        $unidade = new UnidadeProducao();

        $fillable = [
            'nome_cultura',
            'area_total_ha',
            'coordenadas_geograficas',
            'propriedade_id',
        ];

        $this->assertEquals($fillable, $unidade->getFillable());
    }

    public function test_area_total_ha_e_cast_para_decimal(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'Maria Agricultora',
            'cpf_cnpj' => '555.666.777-88',
        ]);

        $propriedade = Propriedade::create([
            'nome' => 'Sítio Esperança',
            'municipio' => 'Crato',
            'uf' => 'CE',
            'area_total' => 80.00,
            'produtor_id' => $produtor->id,
        ]);

        $unidade = UnidadeProducao::create([
            'nome_cultura' => 'Feijão',
            'area_total_ha' => '45.75',
            'propriedade_id' => $propriedade->id,
        ]);

        $this->assertEquals('45.75', $unidade->area_total_ha);
    }

    public function test_tem_relacionamento_belongs_to_com_propriedade(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'Carlos Plantador',
            'cpf_cnpj' => '999.888.777-66',
        ]);

        $propriedade = Propriedade::create([
            'nome' => 'Chácara Verde Vida',
            'municipio' => 'Juazeiro do Norte',
            'uf' => 'CE',
            'area_total' => 150.00,
            'produtor_id' => $produtor->id,
        ]);

        $unidade = UnidadeProducao::create([
            'nome_cultura' => 'Soja',
            'area_total_ha' => 60.00,
            'propriedade_id' => $propriedade->id,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $unidade->propriedade());
        $this->assertInstanceOf(Propriedade::class, $unidade->propriedade);
        $this->assertEquals($propriedade->id, $unidade->propriedade->id);
        $this->assertEquals('Chácara Verde Vida', $unidade->propriedade->nome);
    }

    public function test_usa_tabela_unidades_producao(): void
    {
        $unidade = new UnidadeProducao();
        $this->assertEquals('unidades_producao', $unidade->getTable());
    }

    public function test_coordenadas_geograficas_pode_ser_null(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'Ana Cultivadora',
            'cpf_cnpj' => '222.333.444-55',
        ]);

        $propriedade = Propriedade::create([
            'nome' => 'Roça Feliz',
            'municipio' => 'Iguatu',
            'uf' => 'CE',
            'area_total' => 25.00,
            'produtor_id' => $produtor->id,
        ]);

        $unidade = UnidadeProducao::create([
            'nome_cultura' => 'Algodão',
            'area_total_ha' => 10.50,
            'coordenadas_geograficas' => null,
            'propriedade_id' => $propriedade->id,
        ]);

        $this->assertNull($unidade->coordenadas_geograficas);
        $this->assertDatabaseHas('unidades_producao', [
            'nome_cultura' => 'Algodão',
            'coordenadas_geograficas' => null,
        ]);
    }

    public function test_pode_ter_multiplas_unidades_na_mesma_propriedade(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'Pedro Diversificado',
            'cpf_cnpj' => '444.555.666-77',
        ]);

        $propriedade = Propriedade::create([
            'nome' => 'Fazenda Policultura',
            'municipio' => 'Quixadá',
            'uf' => 'CE',
            'area_total' => 300.00,
            'produtor_id' => $produtor->id,
        ]);

        $unidade1 = UnidadeProducao::create([
            'nome_cultura' => 'Milho',
            'area_total_ha' => 100.00,
            'propriedade_id' => $propriedade->id,
        ]);

        $unidade2 = UnidadeProducao::create([
            'nome_cultura' => 'Feijão',
            'area_total_ha' => 80.00,
            'propriedade_id' => $propriedade->id,
        ]);

        $unidade3 = UnidadeProducao::create([
            'nome_cultura' => 'Mandioca',
            'area_total_ha' => 50.00,
            'propriedade_id' => $propriedade->id,
        ]);

        $this->assertDatabaseCount('unidades_producao', 3);
        $this->assertEquals(3, $propriedade->unidadesProducao()->count());
    }
}
