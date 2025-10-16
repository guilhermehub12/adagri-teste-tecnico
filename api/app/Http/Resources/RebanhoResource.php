<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RebanhoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'especie' => $this->especie,
            'quantidade' => $this->quantidade,
            'finalidade' => $this->finalidade,
            'data_atualizacao' => $this->data_atualizacao?->toISOString(),
            'propriedade_id' => $this->propriedade_id,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
