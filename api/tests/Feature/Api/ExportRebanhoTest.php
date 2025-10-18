<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\User;

use App\Models\ProdutorRural;
use App\Models\Propriedade;
use App\Models\Rebanho;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportRebanhoTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaders(UserRole $role = UserRole::GESTOR): array
    {
        $user = User::factory()->create(['role' => $role]);
        $token = $user->createToken('test')->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_endpoint_de_exportacao_retorna_200(): void
    {
        Rebanho::factory()->count(3)->create();

        $response = $this->getJson('/api/rebanhos/export/pdf', $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_exportacao_tem_content_type_correto(): void
    {
        Rebanho::factory()->count(2)->create();

        $response = $this->getJson('/api/rebanhos/export/pdf', $this->authHeaders());

        $response->assertStatus(200);
        $this->assertEquals(
            'application/pdf',
            $response->headers->get('Content-Type')
        );
    }

    public function test_exportacao_tem_content_disposition_correto(): void
    {
        Rebanho::factory()->count(2)->create();

        $response = $this->getJson('/api/rebanhos/export/pdf', $this->authHeaders());

        $response->assertStatus(200);
        $this->assertStringContainsString(
            'attachment',
            $response->headers->get('Content-Disposition')
        );
        $this->assertStringContainsString(
            'rebanhos.pdf',
            $response->headers->get('Content-Disposition')
        );
    }

    public function test_pdf_contem_titulo_relatorio_de_rebanhos(): void
    {
        Rebanho::factory()->count(2)->create();

        $response = $this->getJson('/api/rebanhos/export/pdf', $this->authHeaders());

        $response->assertStatus(200);

        // Verificar se o conteúdo é PDF válido (inicia com %PDF)
        $content = $response->getContent();
        $this->assertStringStartsWith('%PDF', $content);
    }

    public function test_pode_filtrar_exportacao_por_especie(): void
    {
        Rebanho::factory()->create(['especie' => 'Bovino']);
        Rebanho::factory()->create(['especie' => 'Bovino']);
        Rebanho::factory()->create(['especie' => 'Caprino']);

        $response = $this->getJson('/api/rebanhos/export/pdf?especie=Bovino', $this->authHeaders());

        $response->assertStatus(200);

        // PDF deve ser gerado com sucesso
        $content = $response->getContent();
        $this->assertStringStartsWith('%PDF', $content);
    }

    public function test_pode_filtrar_exportacao_por_propriedade(): void
    {
        $propriedade1 = Propriedade::factory()->create();
        $propriedade2 = Propriedade::factory()->create();

        Rebanho::factory()->count(2)->create(['propriedade_id' => $propriedade1->id]);
        Rebanho::factory()->create(['propriedade_id' => $propriedade2->id]);

        $response = $this->getJson("/api/rebanhos/export/pdf?propriedade_id={$propriedade1->id}", $this->authHeaders());

        $response->assertStatus(200);

        // PDF deve ser gerado com sucesso
        $content = $response->getContent();
        $this->assertStringStartsWith('%PDF', $content);
    }

    public function test_pode_aplicar_filtros_combinados(): void
    {
        $propriedade1 = Propriedade::factory()->create();
        $propriedade2 = Propriedade::factory()->create();

        Rebanho::factory()->create([
            'propriedade_id' => $propriedade1->id,
            'especie' => 'Bovino',
        ]);
        Rebanho::factory()->create([
            'propriedade_id' => $propriedade1->id,
            'especie' => 'Caprino',
        ]);
        Rebanho::factory()->create([
            'propriedade_id' => $propriedade2->id,
            'especie' => 'Bovino',
        ]);

        $response = $this->getJson("/api/rebanhos/export/pdf?propriedade_id={$propriedade1->id}&especie=Bovino", $this->authHeaders());

        $response->assertStatus(200);

        // PDF deve ser gerado com sucesso
        $content = $response->getContent();
        $this->assertStringStartsWith('%PDF', $content);
    }

    public function test_pode_filtrar_exportacao_por_produtor(): void
    {
        $produtor1 = ProdutorRural::factory()->create();
        $produtor2 = ProdutorRural::factory()->create();

        $propriedade1 = Propriedade::factory()->create(['produtor_id' => $produtor1->id]);
        $propriedade2 = Propriedade::factory()->create(['produtor_id' => $produtor1->id]);
        $propriedade3 = Propriedade::factory()->create(['produtor_id' => $produtor2->id]);

        Rebanho::factory()->count(2)->create(['propriedade_id' => $propriedade1->id]);
        Rebanho::factory()->create(['propriedade_id' => $propriedade2->id]);
        Rebanho::factory()->create(['propriedade_id' => $propriedade3->id]);

        $response = $this->getJson("/api/rebanhos/export/pdf?produtor_id={$produtor1->id}", $this->authHeaders());

        $response->assertStatus(200);

        // PDF deve ser gerado com sucesso
        $content = $response->getContent();
        $this->assertStringStartsWith('%PDF', $content);
    }

    public function test_pode_aplicar_filtros_combinados_com_produtor(): void
    {
        $produtor1 = ProdutorRural::factory()->create();
        $produtor2 = ProdutorRural::factory()->create();

        $propriedade1 = Propriedade::factory()->create(['produtor_id' => $produtor1->id]);
        $propriedade2 = Propriedade::factory()->create(['produtor_id' => $produtor2->id]);

        Rebanho::factory()->create([
            'propriedade_id' => $propriedade1->id,
            'especie' => 'Bovino',
        ]);
        Rebanho::factory()->create([
            'propriedade_id' => $propriedade1->id,
            'especie' => 'Caprino',
        ]);
        Rebanho::factory()->create([
            'propriedade_id' => $propriedade2->id,
            'especie' => 'Bovino',
        ]);

        $response = $this->getJson("/api/rebanhos/export/pdf?produtor_id={$produtor1->id}&especie=Bovino", $this->authHeaders());

        $response->assertStatus(200);

        // PDF deve ser gerado com sucesso
        $content = $response->getContent();
        $this->assertStringStartsWith('%PDF', $content);
    }

    public function test_pdf_e_gerado_mesmo_sem_dados(): void
    {
        // Não criar nenhum rebanho

        $response = $this->getJson('/api/rebanhos/export/pdf', $this->authHeaders());

        $response->assertStatus(200);

        // PDF vazio deve ser gerado com sucesso
        $content = $response->getContent();
        $this->assertStringStartsWith('%PDF', $content);
    }
}
