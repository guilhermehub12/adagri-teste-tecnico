<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnidadeProducaoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome_cultura' => $this->nome_cultura,
            'area_total_ha' => $this->area_total_ha,
            'coordenadas_geograficas' => $this->coordenadas_geograficas,
            'propriedade_id' => $this->propriedade_id,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
