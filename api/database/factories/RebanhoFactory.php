<?php

namespace Database\Factories;

use App\Models\Propriedade;
use Illuminate\Database\Eloquent\Factories\Factory;

class RebanhoFactory extends Factory
{
    public function definition(): array
    {
        $especies = ['Bovino', 'Caprino', 'Ovino', 'Suíno', 'Aves', 'Equino'];
        $finalidades = ['Corte', 'Leite', 'Ovos', 'Reprodução', 'Trabalho'];

        return [
            'especie' => fake()->randomElement($especies),
            'quantidade' => fake()->numberBetween(10, 500),
            'finalidade' => fake()->optional()->randomElement($finalidades),
            'data_atualizacao' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'propriedade_id' => Propriedade::factory(),
        ];
    }
}
