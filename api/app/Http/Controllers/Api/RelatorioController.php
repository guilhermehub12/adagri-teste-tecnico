<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RelatorioAnimaisEspecieResource;
use App\Http\Resources\RelatorioHectaresCulturaResource;
use App\Http\Resources\RelatorioPropriedadesMunicipioResource;
use App\Models\Propriedade;
use App\Models\Rebanho;
use App\Models\UnidadeProducao;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class RelatorioController extends Controller
{
    public function propriedadesPorMunicipio(Request $request): AnonymousResourceCollection
    {
        $query = Propriedade::select(
            'municipio',
            'uf',
            DB::raw('COUNT(*) as total_propriedades'),
            DB::raw('SUM(area_total) as area_total_ha'),
            DB::raw('COUNT(DISTINCT produtor_id) as total_produtores')
        )
            ->groupBy('municipio', 'uf');

        // Aplicar filtros
        if ($request->filled('uf')) {
            $query->where('uf', $request->input('uf'));
        }

        if ($request->filled('municipio')) {
            $query->where('municipio', $request->input('municipio'));
        }

        $resultados = $query->orderBy('municipio', 'asc')->get();

        return RelatorioPropriedadesMunicipioResource::collection($resultados);
    }

    public function animaisPorEspecie(Request $request): AnonymousResourceCollection
    {
        $query = Rebanho::select(
            'especie',
            DB::raw('SUM(quantidade) as total_animais'),
            DB::raw('COUNT(*) as total_rebanhos'),
            DB::raw('COUNT(DISTINCT propriedade_id) as total_propriedades'),
            DB::raw('AVG(quantidade) as media_animais_por_rebanho')
        )
            ->groupBy('especie');

        // Aplicar filtro por espécie
        if ($request->filled('especie')) {
            $query->where('especie', $request->input('especie'));
        }

        $resultados = $query->orderBy('especie', 'asc')->get();

        return RelatorioAnimaisEspecieResource::collection($resultados);
    }

    public function hectaresPorCultura(Request $request): AnonymousResourceCollection
    {
        $query = UnidadeProducao::select(
            'nome_cultura',
            DB::raw('SUM(area_total_ha) as total_hectares'),
            DB::raw('COUNT(*) as total_unidades'),
            DB::raw('COUNT(DISTINCT propriedade_id) as total_propriedades'),
            DB::raw('AVG(area_total_ha) as media_hectares_por_unidade')
        )
            ->groupBy('nome_cultura');

        // Aplicar filtro por cultura
        if ($request->filled('cultura')) {
            $query->where('nome_cultura', $request->input('cultura'));
        }

        $resultados = $query->orderBy('nome_cultura', 'asc')->get();

        return RelatorioHectaresCulturaResource::collection($resultados);
    }
}
