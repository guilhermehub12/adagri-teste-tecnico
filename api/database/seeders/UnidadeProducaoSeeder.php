<?php

namespace Database\Seeders;

use App\Models\Propriedade;
use App\Models\UnidadeProducao;
use Illuminate\Database\Seeder;

class UnidadeProducaoSeeder extends Seeder
{
    public function run(): void
    {
        $propriedades = Propriedade::all();
        $culturas = ['Milho', 'Soja', 'Feijão', 'Café', 'Mandioca', 'Caju', 'Algodão', 'Arroz'];

        $unidades = [];

        // Criar 60 unidades de produção
        for ($i = 1; $i <= 60; $i++) {
            $propriedade = $propriedades->random();
            $cultura = $culturas[array_rand($culturas)];

            // Área da unidade deve ser menor ou igual à área total da propriedade
            $areaMaxima = $propriedade->area_total * 0.8; // máximo 80% da propriedade
            $area = round(rand(5 * 100, $areaMaxima * 100) / 100, 2);

            $coordenadas = null;
            if (rand(0, 1)) {
                // Algumas unidades terão coordenadas
                $lat = round(rand(-8000000, -2000000) / 100000, 6); // Latitude CE: -8 a -2
                $lon = round(rand(-41000000, -37000000) / 100000, 6); // Longitude CE: -41 a -37
                $coordenadas = "{$lat},{$lon}";
            }

            $unidades[] = [
                'nome_cultura' => $cultura,
                'area_total_ha' => $area,
                'coordenadas_geograficas' => $coordenadas,
                'propriedade_id' => $propriedade->id,
            ];
        }

        foreach ($unidades as $unidade) {
            UnidadeProducao::create($unidade);
        }
    }
}
