<?php

namespace Tests\Unit\Models;

use App\Models\ProdutorRural;
use App\Models\Propriedade;
use App\Models\Rebanho;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RebanhoTest extends TestCase
{
    use RefreshDatabase;

    public function test_pode_criar_rebanho(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'João Pecuarista',
            'cpf_cnpj' => '111.222.333-44',
        ]);

        $propriedade = Propriedade::create([
            'nome' => 'Fazenda Gado Forte',
            'municipio' => 'Canindé',
            'uf' => 'CE',
            'area_total' => 800.00,
            'produtor_id' => $produtor->id,
        ]);

        $rebanho = Rebanho::create([
            'especie' => 'Bovino',
            'quantidade' => 150,
            'finalidade' => 'Corte',
            'data_atualizacao' => '2025-10-15 10:00:00',
            'propriedade_id' => $propriedade->id,
        ]);

        $this->assertInstanceOf(Rebanho::class, $rebanho);
        $this->assertEquals('Bovino', $rebanho->especie);
        $this->assertEquals(150, $rebanho->quantidade);
        $this->assertEquals('Corte', $rebanho->finalidade);
        $this->assertDatabaseHas('rebanhos', [
            'especie' => 'Bovino',
            'quantidade' => 150,
            'finalidade' => 'Corte',
        ]);
    }

    public function test_campos_sao_fillable(): void
    {
        $rebanho = new Rebanho();

        $fillable = [
            'especie',
            'quantidade',
            'finalidade',
            'data_atualizacao',
            'propriedade_id',
        ];

        $this->assertEquals($fillable, $rebanho->getFillable());
    }

    public function test_quantidade_e_cast_para_integer(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'Maria Criadora',
            'cpf_cnpj' => '555.666.777-88',
        ]);

        $propriedade = Propriedade::create([
            'nome' => 'Sítio Caprino Alegre',
            'municipio' => 'Tauá',
            'uf' => 'CE',
            'area_total' => 120.00,
            'produtor_id' => $produtor->id,
        ]);

        $rebanho = Rebanho::create([
            'especie' => 'Caprino',
            'quantidade' => '75',
            'propriedade_id' => $propriedade->id,
        ]);

        $this->assertIsInt($rebanho->quantidade);
        $this->assertEquals(75, $rebanho->quantidade);
    }

    public function test_data_atualizacao_e_cast_para_datetime(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'Carlos Pastor',
            'cpf_cnpj' => '999.888.777-66',
        ]);

        $propriedade = Propriedade::create([
            'nome' => 'Fazenda Ovelha Feliz',
            'municipio' => 'Limoeiro do Norte',
            'uf' => 'CE',
            'area_total' => 200.00,
            'produtor_id' => $produtor->id,
        ]);

        $rebanho = Rebanho::create([
            'especie' => 'Ovino',
            'quantidade' => 200,
            'data_atualizacao' => '2025-10-15 14:30:00',
            'propriedade_id' => $propriedade->id,
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $rebanho->data_atualizacao);
    }

    public function test_tem_relacionamento_belongs_to_com_propriedade(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'Ana Avicultora',
            'cpf_cnpj' => '222.333.444-55',
        ]);

        $propriedade = Propriedade::create([
            'nome' => 'Granja Galinha Caipira',
            'municipio' => 'Aracati',
            'uf' => 'CE',
            'area_total' => 50.00,
            'produtor_id' => $produtor->id,
        ]);

        $rebanho = Rebanho::create([
            'especie' => 'Aves',
            'quantidade' => 5000,
            'finalidade' => 'Ovos',
            'propriedade_id' => $propriedade->id,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $rebanho->propriedade());
        $this->assertInstanceOf(Propriedade::class, $rebanho->propriedade);
        $this->assertEquals($propriedade->id, $rebanho->propriedade->id);
        $this->assertEquals('Granja Galinha Caipira', $rebanho->propriedade->nome);
    }

    public function test_usa_tabela_rebanhos(): void
    {
        $rebanho = new Rebanho();
        $this->assertEquals('rebanhos', $rebanho->getTable());
    }

    public function test_finalidade_pode_ser_null(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'Pedro Suinocultor',
            'cpf_cnpj' => '444.555.666-77',
        ]);

        $propriedade = Propriedade::create([
            'nome' => 'Chácara Porco Feliz',
            'municipio' => 'Russas',
            'uf' => 'CE',
            'area_total' => 30.00,
            'produtor_id' => $produtor->id,
        ]);

        $rebanho = Rebanho::create([
            'especie' => 'Suíno',
            'quantidade' => 40,
            'finalidade' => null,
            'propriedade_id' => $propriedade->id,
        ]);

        $this->assertNull($rebanho->finalidade);
        $this->assertDatabaseHas('rebanhos', [
            'especie' => 'Suíno',
            'finalidade' => null,
        ]);
    }

    public function test_pode_ter_multiplos_rebanhos_na_mesma_propriedade(): void
    {
        $produtor = ProdutorRural::create([
            'nome' => 'Lucas Diversificado',
            'cpf_cnpj' => '777.888.999-00',
        ]);

        $propriedade = Propriedade::create([
            'nome' => 'Fazenda Mista',
            'municipio' => 'Morada Nova',
            'uf' => 'CE',
            'area_total' => 600.00,
            'produtor_id' => $produtor->id,
        ]);

        $rebanho1 = Rebanho::create([
            'especie' => 'Bovino',
            'quantidade' => 100,
            'finalidade' => 'Leite',
            'propriedade_id' => $propriedade->id,
        ]);

        $rebanho2 = Rebanho::create([
            'especie' => 'Caprino',
            'quantidade' => 50,
            'finalidade' => 'Leite',
            'propriedade_id' => $propriedade->id,
        ]);

        $rebanho3 = Rebanho::create([
            'especie' => 'Aves',
            'quantidade' => 1000,
            'finalidade' => 'Ovos',
            'propriedade_id' => $propriedade->id,
        ]);

        $this->assertDatabaseCount('rebanhos', 3);
        $this->assertEquals(3, $propriedade->rebanhos()->count());
    }
}
