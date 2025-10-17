<?php

namespace Database\Seeders;

use App\Models\ProdutorRural;
use Illuminate\Database\Seeder;

class ProdutorRuralSeeder extends Seeder
{
    public function run(): void
    {
        $produtores = [
            ['cpf_cnpj' => '12345678901', 'nome' => 'João Silva Santos', 'email' => 'joao.silva@email.com', 'telefone' => '85987654321'],
            ['cpf_cnpj' => '98765432100', 'nome' => 'Maria Oliveira Costa', 'email' => 'maria.oliveira@email.com', 'telefone' => '85987654322'],
            ['cpf_cnpj' => '11223344556', 'nome' => 'Pedro Alves Lima', 'email' => 'pedro.alves@email.com', 'telefone' => '85987654323'],
            ['cpf_cnpj' => '22334455667', 'nome' => 'Ana Paula Ferreira', 'email' => 'ana.ferreira@email.com', 'telefone' => '85987654324'],
            ['cpf_cnpj' => '33445566778', 'nome' => 'Carlos Eduardo Souza', 'email' => 'carlos.souza@email.com', 'telefone' => '85987654325'],
            ['cpf_cnpj' => '12345678000190', 'nome' => 'Agropecuária Vale Verde Ltda', 'email' => 'contato@valeverde.com.br', 'telefone' => '85987654326'],
            ['cpf_cnpj' => '98765432000180', 'nome' => 'Fazenda São José LTDA', 'email' => 'contato@fazendasaojose.com.br', 'telefone' => '85987654327'],
            ['cpf_cnpj' => '44556677889', 'nome' => 'Francisco Araújo Neto', 'email' => 'francisco.araujo@email.com', 'telefone' => '85987654328'],
            ['cpf_cnpj' => '55667788990', 'nome' => 'Antônia Bezerra Rocha', 'email' => 'antonia.bezerra@email.com', 'telefone' => '85987654329'],
            ['cpf_cnpj' => '66778899001', 'nome' => 'José Carlos Mendes', 'email' => 'jose.mendes@email.com', 'telefone' => '85987654330'],
            ['cpf_cnpj' => '77889900112', 'nome' => 'Mariana Gomes Pereira', 'email' => 'mariana.gomes@email.com', 'telefone' => '85987654331'],
            ['cpf_cnpj' => '11122233000145', 'nome' => 'Cooperativa Agrícola do Sertão', 'email' => 'contato@coopsertao.com.br', 'telefone' => '85987654332'],
            ['cpf_cnpj' => '88990011223', 'nome' => 'Ricardo Fernandes Silva', 'email' => 'ricardo.fernandes@email.com', 'telefone' => '85987654333'],
            ['cpf_cnpj' => '99001122334', 'nome' => 'Juliana Cardoso Almeida', 'email' => 'juliana.cardoso@email.com', 'telefone' => '85987654334'],
            ['cpf_cnpj' => '00112233445', 'nome' => 'Roberto Andrade Costa', 'email' => 'roberto.andrade@email.com', 'telefone' => '85987654335'],
            ['cpf_cnpj' => '22233344000156', 'nome' => 'Granja Santa Rita S.A.', 'email' => 'contato@granjasantarita.com.br', 'telefone' => '85987654336'],
            ['cpf_cnpj' => '10203040556', 'nome' => 'Sebastião Moreira Lima', 'email' => 'sebastiao.moreira@email.com', 'telefone' => '85987654337'],
            ['cpf_cnpj' => '20304050667', 'nome' => 'Francisca Rodrigues Santos', 'email' => 'francisca.rodrigues@email.com', 'telefone' => '85987654338'],
            ['cpf_cnpj' => '30405060778', 'nome' => 'Luiz Fernando Barbosa', 'email' => 'luiz.barbosa@email.com', 'telefone' => '85987654339'],
            ['cpf_cnpj' => '40506070889', 'nome' => 'Cristina Alves Teixeira', 'email' => 'cristina.teixeira@email.com', 'telefone' => '85987654340'],
        ];

        foreach ($produtores as $produtor) {
            ProdutorRural::create($produtor);
        }
    }
}
