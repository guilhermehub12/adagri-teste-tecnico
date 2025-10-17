<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RelatorioHectaresCulturaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'cultura' => $this->nome_cultura,
            'total_hectares' => (float) $this->total_hectares,
            'total_unidades' => (int) $this->total_unidades,
            'total_propriedades' => (int) $this->total_propriedades,
            'media_hectares_por_unidade' => (float) $this->media_hectares_por_unidade,
        ];
    }
}
