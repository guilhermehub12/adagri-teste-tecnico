<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProdutorRural;
use App\Models\Propriedade;
use App\Models\Rebanho;
use App\Models\UnidadeProducao;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Retorna estatísticas agregadas do dashboard
     *
     * Este endpoint retorna apenas números agregados, sem expor dados sensíveis
     * como CPF, email, nome, etc.
     */
    public function stats(): JsonResponse
    {
        $totalProdutores = ProdutorRural::count();
        $totalPropriedades = Propriedade::count();
        $totalAnimais = Rebanho::sum('quantidade') ?? 0;
        $totalHectares = UnidadeProducao::sum('area_total_ha') ?? 0;

        return response()->json([
            'totalProdutores' => $totalProdutores,
            'totalPropriedades' => $totalPropriedades,
            'totalAnimais' => (int) $totalAnimais,
            'totalHectares' => (float) $totalHectares,
        ]);
    }
}
