<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropriedadeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'municipio' => $this->municipio,
            'uf' => $this->uf,
            'inscricao_estadual' => $this->inscricao_estadual,
            'area_total' => $this->area_total,
            'produtor_id' => $this->produtor_id,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
