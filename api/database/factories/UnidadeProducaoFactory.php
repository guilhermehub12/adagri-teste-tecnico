<?php

namespace Database\Factories;

use App\Models\Propriedade;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnidadeProducaoFactory extends Factory
{
    public function definition(): array
    {
        $culturas = ['Milho', 'Feijão', 'Soja', 'Algodão', 'Arroz', 'Mandioca', 'Cana-de-açúcar'];

        return [
            'nome_cultura' => fake()->randomElement($culturas),
            'area_total_ha' => fake()->randomFloat(2, 5, 500),
            'coordenadas_geograficas' => fake()->optional()->latitude() . ', ' . fake()->optional()->longitude(),
            'propriedade_id' => Propriedade::factory(),
        ];
    }
}
