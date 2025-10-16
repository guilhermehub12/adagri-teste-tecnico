<?php

namespace Database\Factories;

use App\Models\ProdutorRural;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropriedadeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->company(),
            'municipio' => fake()->city(),
            'uf' => fake()->stateAbbr(),
            'inscricao_estadual' => fake()->optional()->numerify('IE-########'),
            'area_total' => fake()->randomFloat(2, 10, 1000),
            'produtor_id' => ProdutorRural::factory(),
        ];
    }
}
