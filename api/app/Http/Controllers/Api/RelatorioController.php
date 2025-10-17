<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RelatorioPropriedadesMunicipioResource;
use App\Models\Propriedade;
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
}
