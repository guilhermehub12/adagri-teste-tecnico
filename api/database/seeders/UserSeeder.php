<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Senha padrão para testes: password123
        $password = Hash::make('password123');

        User::create([
            'name' => 'Admin do Sistema',
            'email' => 'admin@adagri.ce.gov.br',
            'password' => $password,
            'role' => UserRole::ADMIN,
        ]);

        User::create([
            'name' => 'Gestor Agropecuário',
            'email' => 'gestor@adagri.ce.gov.br',
            'password' => $password,
            'role' => UserRole::GESTOR,
        ]);

        User::create([
            'name' => 'Técnico de Campo',
            'email' => 'tecnico@adagri.ce.gov.br',
            'password' => $password,
            'role' => UserRole::TECNICO,
        ]);

        User::create([
            'name' => 'Extensionista Rural',
            'email' => 'extensionista@adagri.ce.gov.br',
            'password' => $password,
            'role' => UserRole::EXTENSIONISTA,
        ]);
    }
}
