<?php

namespace Tests\Feature\Api;

use App\Models\ProdutorRural;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProdutorFotoTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Fake storage for testing
        Storage::fake('public');

        $this->user = User::factory()->create();
    }

    public function test_pode_criar_produtor_sem_foto(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/produtores-rurais', [
                'nome' => 'João Silva',
                'cpf_cnpj' => '123.456.789-00',
                'telefone' => '(85) 99999-9999',
                'email' => 'joao@example.com',
                'endereco' => 'Rua Teste, 123',
            ]);

        $response->assertStatus(201);
        $this->assertNull($response->json('data.foto'));
    }

    public function test_pode_fazer_upload_de_foto(): void
    {
        $produtor = ProdutorRural::factory()->create();
        $foto = UploadedFile::fake()->image('produtor.jpg', 800, 600);

        $response = $this->actingAs($this->user)
            ->postJson("/api/produtores/{$produtor->id}/foto", [
                'foto' => $foto,
            ]);

        $response->assertStatus(200);

        // Verifica se a foto foi salva
        $produtor->refresh();
        $this->assertNotNull($produtor->foto);

        // Verifica se o arquivo existe no storage
        Storage::disk('public')->assertExists('produtores/' . basename($produtor->foto));

        // Verifica se a resposta inclui a URL da foto
        $this->assertNotNull($response->json('data.foto_url'));
    }

    public function test_upload_aceita_apenas_imagens(): void
    {
        $produtor = ProdutorRural::factory()->create();
        $arquivo = UploadedFile::fake()->create('documento.pdf', 1024);

        $response = $this->actingAs($this->user)
            ->postJson("/api/produtores/{$produtor->id}/foto", [
                'foto' => $arquivo,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['foto']);
    }

    public function test_upload_valida_tamanho_maximo(): void
    {
        $produtor = ProdutorRural::factory()->create();
        // Imagem maior que 2MB
        $foto = UploadedFile::fake()->image('foto_grande.jpg')->size(3000);

        $response = $this->actingAs($this->user)
            ->postJson("/api/produtores/{$produtor->id}/foto", [
                'foto' => $foto,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['foto']);
    }

    public function test_upload_requer_arquivo(): void
    {
        $produtor = ProdutorRural::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson("/api/produtores/{$produtor->id}/foto", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['foto']);
    }

    public function test_upload_substitui_foto_anterior(): void
    {
        $produtor = ProdutorRural::factory()->create();

        // Primeira foto
        $primeiraFoto = UploadedFile::fake()->image('foto1.jpg');
        $this->actingAs($this->user)
            ->postJson("/api/produtores/{$produtor->id}/foto", [
                'foto' => $primeiraFoto,
            ]);

        $produtor->refresh();
        $caminhoAnterior = $produtor->foto;

        // Segunda foto (deve substituir)
        $segundaFoto = UploadedFile::fake()->image('foto2.jpg');
        $response = $this->actingAs($this->user)
            ->postJson("/api/produtores/{$produtor->id}/foto", [
                'foto' => $segundaFoto,
            ]);

        $response->assertStatus(200);

        $produtor->refresh();
        $this->assertNotEquals($caminhoAnterior, $produtor->foto);

        // Verifica se a foto antiga foi deletada
        Storage::disk('public')->assertMissing('produtores/' . basename($caminhoAnterior));

        // Verifica se a nova foto existe
        Storage::disk('public')->assertExists('produtores/' . basename($produtor->foto));
    }

    public function test_pode_remover_foto(): void
    {
        $produtor = ProdutorRural::factory()->create();

        // Upload de foto
        $foto = UploadedFile::fake()->image('produtor.jpg');
        $this->actingAs($this->user)
            ->postJson("/api/produtores/{$produtor->id}/foto", [
                'foto' => $foto,
            ]);

        $produtor->refresh();
        $caminhoFoto = $produtor->foto;

        // Remove foto
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/produtores/{$produtor->id}/foto");

        $response->assertStatus(200);

        $produtor->refresh();
        $this->assertNull($produtor->foto);

        // Verifica se o arquivo foi deletado
        Storage::disk('public')->assertMissing('produtores/' . basename($caminhoFoto));
    }

    public function test_remover_foto_quando_nao_existe_retorna_404(): void
    {
        $produtor = ProdutorRural::factory()->create(['foto' => null]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/produtores/{$produtor->id}/foto");

        $response->assertStatus(404);
    }

    public function test_produtor_resource_inclui_url_da_foto(): void
    {
        $produtor = ProdutorRural::factory()->create();

        $foto = UploadedFile::fake()->image('produtor.jpg');
        $this->actingAs($this->user)
            ->postJson("/api/produtores/{$produtor->id}/foto", [
                'foto' => $foto,
            ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/produtores-rurais/{$produtor->id}");

        $response->assertStatus(200);
        $this->assertNotNull($response->json('data.foto_url'));
        $this->assertStringContainsString('/storage/produtores/', $response->json('data.foto_url'));
    }

    public function test_produtor_sem_foto_retorna_null_na_url(): void
    {
        $produtor = ProdutorRural::factory()->create(['foto' => null]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/produtores-rurais/{$produtor->id}");

        $response->assertStatus(200);
        $this->assertNull($response->json('data.foto_url'));
    }

    public function test_ao_deletar_produtor_foto_e_removida(): void
    {
        $produtor = ProdutorRural::factory()->create();

        $foto = UploadedFile::fake()->image('produtor.jpg');
        $this->actingAs($this->user)
            ->postJson("/api/produtores/{$produtor->id}/foto", [
                'foto' => $foto,
            ]);

        $produtor->refresh();
        $caminhoFoto = $produtor->foto;

        // Deleta o produtor
        $this->actingAs($this->user)
            ->deleteJson("/api/produtores-rurais/{$produtor->id}");

        // Verifica se a foto foi removida do storage
        Storage::disk('public')->assertMissing('produtores/' . basename($caminhoFoto));
    }
}
