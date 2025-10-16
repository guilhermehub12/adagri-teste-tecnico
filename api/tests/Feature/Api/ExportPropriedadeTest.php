<?php

namespace Tests\Feature\Api;

use App\Models\ProdutorRural;
use App\Models\Propriedade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ExportPropriedadeTest extends TestCase
{
    use RefreshDatabase;

    public function test_endpoint_de_exportacao_retorna_200(): void
    {
        Propriedade::factory()->count(3)->create();

        $response = $this->getJson('/api/propriedades/export/excel');

        $response->assertStatus(200);
    }

    public function test_exportacao_tem_content_type_correto(): void
    {
        Propriedade::factory()->count(2)->create();

        $response = $this->getJson('/api/propriedades/export/excel');

        $response->assertStatus(200);
        $this->assertEquals(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type')
        );
    }

    public function test_exportacao_tem_content_disposition_correto(): void
    {
        Propriedade::factory()->count(2)->create();

        $response = $this->getJson('/api/propriedades/export/excel');

        $response->assertStatus(200);
        $this->assertStringContainsString(
            'attachment',
            $response->headers->get('Content-Disposition')
        );
        $this->assertStringContainsString(
            'propriedades.xlsx',
            $response->headers->get('Content-Disposition')
        );
    }

    public function test_excel_contem_colunas_esperadas(): void
    {
        Excel::fake();

        $produtor = ProdutorRural::factory()->create(['nome' => 'João Silva']);

        Propriedade::factory()->create([
            'nome' => 'Fazenda Boa Vista',
            'municipio' => 'Fortaleza',
            'uf' => 'CE',
            'inscricao_estadual' => 'IE-123456',
            'area_total' => 100.50,
            'produtor_id' => $produtor->id,
        ]);

        $this->getJson('/api/propriedades/export/excel');

        Excel::assertDownloaded('propriedades.xlsx', function($export) {
            $headings = $export->headings();

            return in_array('ID', $headings) &&
                   in_array('Nome', $headings) &&
                   in_array('Município', $headings) &&
                   in_array('UF', $headings) &&
                   in_array('Inscrição Estadual', $headings) &&
                   in_array('Área Total (ha)', $headings) &&
                   in_array('Produtor', $headings);
        });
    }

    public function test_pode_filtrar_exportacao_por_municipio(): void
    {
        Excel::fake();

        Propriedade::factory()->create(['municipio' => 'Fortaleza']);
        Propriedade::factory()->create(['municipio' => 'Fortaleza']);
        Propriedade::factory()->create(['municipio' => 'Sobral']);

        $this->getJson('/api/propriedades/export/excel?municipio=Fortaleza');

        Excel::assertDownloaded('propriedades.xlsx', function($export) {
            $collection = $export->collection();

            // Deve ter apenas 2 propriedades de Fortaleza
            if ($collection->count() !== 2) {
                return false;
            }

            // Todas devem ser de Fortaleza
            foreach ($collection as $item) {
                if ($item->municipio !== 'Fortaleza') {
                    return false;
                }
            }

            return true;
        });
    }

    public function test_pode_filtrar_exportacao_por_produtor(): void
    {
        Excel::fake();

        $produtor1 = ProdutorRural::factory()->create();
        $produtor2 = ProdutorRural::factory()->create();

        Propriedade::factory()->count(2)->create(['produtor_id' => $produtor1->id]);
        Propriedade::factory()->create(['produtor_id' => $produtor2->id]);

        $this->getJson("/api/propriedades/export/excel?produtor_id={$produtor1->id}");

        Excel::assertDownloaded('propriedades.xlsx', function($export) use ($produtor1) {
            $collection = $export->collection();

            // Deve ter apenas 2 propriedades do produtor1
            if ($collection->count() !== 2) {
                return false;
            }

            // Todas devem ser do produtor1
            foreach ($collection as $item) {
                if ($item->produtor_id !== $produtor1->id) {
                    return false;
                }
            }

            return true;
        });
    }

    public function test_pode_aplicar_filtros_combinados(): void
    {
        Excel::fake();

        $produtor1 = ProdutorRural::factory()->create();
        $produtor2 = ProdutorRural::factory()->create();

        // Produtor 1 com propriedades em Fortaleza e Sobral
        Propriedade::factory()->create([
            'produtor_id' => $produtor1->id,
            'municipio' => 'Fortaleza',
        ]);
        Propriedade::factory()->create([
            'produtor_id' => $produtor1->id,
            'municipio' => 'Sobral',
        ]);

        // Produtor 2 com propriedade em Fortaleza
        Propriedade::factory()->create([
            'produtor_id' => $produtor2->id,
            'municipio' => 'Fortaleza',
        ]);

        $this->getJson("/api/propriedades/export/excel?produtor_id={$produtor1->id}&municipio=Fortaleza");

        Excel::assertDownloaded('propriedades.xlsx', function($export) use ($produtor1) {
            $collection = $export->collection();

            // Deve ter apenas 1 propriedade (produtor1 em Fortaleza)
            if ($collection->count() !== 1) {
                return false;
            }

            $item = $collection->first();
            return $item->produtor_id === $produtor1->id &&
                   $item->municipio === 'Fortaleza';
        });
    }

    public function test_exportacao_contem_dados_corretos(): void
    {
        Excel::fake();

        $produtor = ProdutorRural::factory()->create(['nome' => 'Maria Agricultora']);

        $propriedade = Propriedade::factory()->create([
            'nome' => 'Sítio Santa Clara',
            'municipio' => 'Caucaia',
            'uf' => 'CE',
            'inscricao_estadual' => 'IE-987654',
            'area_total' => 250.75,
            'produtor_id' => $produtor->id,
        ]);

        $this->getJson('/api/propriedades/export/excel');

        Excel::assertDownloaded('propriedades.xlsx', function($export) use ($propriedade, $produtor) {
            $collection = $export->collection();
            $item = $collection->first();

            return $item->id === $propriedade->id &&
                   $item->nome === 'Sítio Santa Clara' &&
                   $item->municipio === 'Caucaia' &&
                   $item->uf === 'CE' &&
                   $item->inscricao_estadual === 'IE-987654' &&
                   (float) $item->area_total === 250.75 &&
                   $item->produtor_id === $produtor->id;
        });
    }
}
