<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RelatorioPropriedadesMunicipioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'municipio' => $this->municipio,
            'uf' => $this->uf,
            'total_propriedades' => (int) $this->total_propriedades,
            'area_total_ha' => (float) $this->area_total_ha,
            'total_produtores' => (int) $this->total_produtores,
        ];
    }
}
