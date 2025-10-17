<?php

namespace Database\Seeders;

use App\Models\Propriedade;
use App\Models\Rebanho;
use Illuminate\Database\Seeder;

class RebanhoSeeder extends Seeder
{
    public function run(): void
    {
        $propriedades = Propriedade::all();
        $especies = [
            'Bovino' => ['Corte', 'Leite', 'Mista'],
            'Caprino' => ['Corte', 'Leite', 'Mista'],
            'Ovino' => ['Corte', 'Lã', 'Mista'],
            'Suíno' => ['Corte', 'Reprodução'],
            'Avícola' => ['Corte', 'Postura', 'Mista'],
        ];

        $rebanhos = [];

        // Criar 80 rebanhos
        for ($i = 1; $i <= 80; $i++) {
            $propriedade = $propriedades->random();
            $especie = array_rand($especies);
            $finalidades = $especies[$especie];
            $finalidade = $finalidades[array_rand($finalidades)];

            // Quantidade varia por espécie
            $quantidade = match ($especie) {
                'Bovino' => rand(50, 500),
                'Caprino' => rand(80, 400),
                'Ovino' => rand(100, 600),
                'Suíno' => rand(100, 800),
                'Avícola' => rand(500, 5000),
            };

            $dataAtualizacao = null;
            if (rand(0, 1)) {
                // 50% dos rebanhos terão data de atualização
                $dataAtualizacao = now()->subDays(rand(1, 365));
            }

            $rebanhos[] = [
                'especie' => $especie,
                'quantidade' => $quantidade,
                'finalidade' => $finalidade,
                'data_atualizacao' => $dataAtualizacao,
                'propriedade_id' => $propriedade->id,
            ];
        }

        foreach ($rebanhos as $rebanho) {
            Rebanho::create($rebanho);
        }
    }
}
