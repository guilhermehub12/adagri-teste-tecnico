<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RelatorioAnimaisEspecieResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'especie' => $this->especie,
            'total_animais' => (int) $this->total_animais,
            'total_rebanhos' => (int) $this->total_rebanhos,
            'total_propriedades' => (int) $this->total_propriedades,
            'media_animais_por_rebanho' => (float) $this->media_animais_por_rebanho,
        ];
    }
}
