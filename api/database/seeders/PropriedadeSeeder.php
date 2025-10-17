<?php

namespace Database\Seeders;

use App\Models\Propriedade;
use App\Models\ProdutorRural;
use Illuminate\Database\Seeder;

class PropriedadeSeeder extends Seeder
{
    public function run(): void
    {
        $produtores = ProdutorRural::all();
        $municipios = [
            'Fortaleza', 'Caucaia', 'Aquiraz', 'Sobral', 'Juazeiro do Norte',
            'Crato', 'Iguatu', 'Quixadá', 'Canindé', 'Itapipoca'
        ];

        $propriedades = [
            ['nome' => 'Fazenda Boa Vista', 'municipio' => 'Fortaleza', 'area_total' => 125.50, 'inscricao_estadual' => '123456789'],
            ['nome' => 'Sítio Santa Clara', 'municipio' => 'Caucaia', 'area_total' => 45.75, 'inscricao_estadual' => '234567890'],
            ['nome' => 'Fazenda São Pedro', 'municipio' => 'Aquiraz', 'area_total' => 280.00, 'inscricao_estadual' => '345678901'],
            ['nome' => 'Chácara Recanto Verde', 'municipio' => 'Fortaleza', 'area_total' => 15.25, 'inscricao_estadual' => null],
            ['nome' => 'Fazenda Vale do Sol', 'municipio' => 'Sobral', 'area_total' => 450.00, 'inscricao_estadual' => '456789012'],
            ['nome' => 'Sítio Flores do Campo', 'municipio' => 'Juazeiro do Norte', 'area_total' => 32.50, 'inscricao_estadual' => '567890123'],
            ['nome' => 'Fazenda Terra Fértil', 'municipio' => 'Crato', 'area_total' => 380.75, 'inscricao_estadual' => '678901234'],
            ['nome' => 'Propriedade Rural Esperança', 'municipio' => 'Iguatu', 'area_total' => 210.00, 'inscricao_estadual' => '789012345'],
            ['nome' => 'Sítio Bela Vista', 'municipio' => 'Quixadá', 'area_total' => 55.00, 'inscricao_estadual' => null],
            ['nome' => 'Fazenda Três Irmãos', 'municipio' => 'Canindé', 'area_total' => 195.50, 'inscricao_estadual' => '890123456'],
            ['nome' => 'Chácara do Vale', 'municipio' => 'Itapipoca', 'area_total' => 22.00, 'inscricao_estadual' => null],
            ['nome' => 'Fazenda Monte Alto', 'municipio' => 'Fortaleza', 'area_total' => 320.00, 'inscricao_estadual' => '901234567'],
            ['nome' => 'Sítio Primavera', 'municipio' => 'Caucaia', 'area_total' => 68.25, 'inscricao_estadual' => '012345678'],
            ['nome' => 'Fazenda Nova Esperança', 'municipio' => 'Sobral', 'area_total' => 500.00, 'inscricao_estadual' => '112233445'],
            ['nome' => 'Propriedade Água Viva', 'municipio' => 'Aquiraz', 'area_total' => 175.50, 'inscricao_estadual' => '223344556'],
            ['nome' => 'Sítio Pôr do Sol', 'municipio' => 'Juazeiro do Norte', 'area_total' => 40.00, 'inscricao_estadual' => null],
            ['nome' => 'Fazenda Serra Verde', 'municipio' => 'Crato', 'area_total' => 425.75, 'inscricao_estadual' => '334455667'],
            ['nome' => 'Chácara Beija-Flor', 'municipio' => 'Fortaleza', 'area_total' => 18.50, 'inscricao_estadual' => null],
            ['nome' => 'Fazenda Rio Claro', 'municipio' => 'Iguatu', 'area_total' => 350.00, 'inscricao_estadual' => '445566778'],
            ['nome' => 'Sítio Lua Nova', 'municipio' => 'Quixadá', 'area_total' => 75.00, 'inscricao_estadual' => '556677889'],
            ['nome' => 'Fazenda Horizonte Azul', 'municipio' => 'Canindé', 'area_total' => 265.25, 'inscricao_estadual' => '667788990'],
            ['nome' => 'Propriedade Renascer', 'municipio' => 'Itapipoca', 'area_total' => 135.50, 'inscricao_estadual' => '778899001'],
            ['nome' => 'Sítio Alegria', 'municipio' => 'Sobral', 'area_total' => 50.75, 'inscricao_estadual' => null],
            ['nome' => 'Fazenda Campo Limpo', 'municipio' => 'Fortaleza', 'area_total' => 290.00, 'inscricao_estadual' => '889900112'],
            ['nome' => 'Chácara Paraíso Verde', 'municipio' => 'Caucaia', 'area_total' => 25.00, 'inscricao_estadual' => null],
            ['nome' => 'Fazenda Aurora', 'municipio' => 'Aquiraz', 'area_total' => 410.50, 'inscricao_estadual' => '990011223'],
            ['nome' => 'Sítio Girassol', 'municipio' => 'Juazeiro do Norte', 'area_total' => 38.00, 'inscricao_estadual' => null],
            ['nome' => 'Fazenda Vista Alegre', 'municipio' => 'Crato', 'area_total' => 335.75, 'inscricao_estadual' => '001122334'],
            ['nome' => 'Propriedade Planalto', 'municipio' => 'Iguatu', 'area_total' => 245.00, 'inscricao_estadual' => '101112233'],
            ['nome' => 'Sítio Raio de Sol', 'municipio' => 'Quixadá', 'area_total' => 62.50, 'inscricao_estadual' => null],
            ['nome' => 'Fazenda Céu Azul', 'municipio' => 'Canindé', 'area_total' => 380.00, 'inscricao_estadual' => '202122334'],
            ['nome' => 'Chácara Flor da Serra', 'municipio' => 'Itapipoca', 'area_total' => 28.75, 'inscricao_estadual' => null],
            ['nome' => 'Fazenda Santo Antônio', 'municipio' => 'Sobral', 'area_total' => 475.50, 'inscricao_estadual' => '303132435'],
            ['nome' => 'Sítio Tranquilidade', 'municipio' => 'Fortaleza', 'area_total' => 42.00, 'inscricao_estadual' => null],
            ['nome' => 'Fazenda Palmeiras', 'municipio' => 'Caucaia', 'area_total' => 215.25, 'inscricao_estadual' => '404142536'],
            ['nome' => 'Propriedade Serra Grande', 'municipio' => 'Aquiraz', 'area_total' => 365.00, 'inscricao_estadual' => '505152637'],
            ['nome' => 'Sítio Felicidade', 'municipio' => 'Juazeiro do Norte', 'area_total' => 48.50, 'inscricao_estadual' => null],
            ['nome' => 'Fazenda Progresso', 'municipio' => 'Crato', 'area_total' => 395.75, 'inscricao_estadual' => '606162738'],
            ['nome' => 'Chácara Natureza Viva', 'municipio' => 'Iguatu', 'area_total' => 35.00, 'inscricao_estadual' => null],
            ['nome' => 'Fazenda Ouro Verde', 'municipio' => 'Quixadá', 'area_total' => 425.00, 'inscricao_estadual' => '707172839'],
        ];

        foreach ($propriedades as $index => $propriedade) {
            // Distribuir propriedades entre os produtores
            $produtor = $produtores[$index % $produtores->count()];

            Propriedade::create([
                'nome' => $propriedade['nome'],
                'municipio' => $propriedade['municipio'],
                'uf' => 'CE',
                'area_total' => $propriedade['area_total'],
                'inscricao_estadual' => $propriedade['inscricao_estadual'],
                'produtor_id' => $produtor->id,
            ]);
        }
    }
}
