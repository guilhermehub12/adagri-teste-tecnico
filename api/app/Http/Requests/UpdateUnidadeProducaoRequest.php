<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUnidadeProducaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome_cultura' => ['required', 'string', 'max:255'],
            'area_total_ha' => ['required', 'numeric', 'min:0'],
            'coordenadas_geograficas' => ['nullable', 'string', 'max:255'],
            'propriedade_id' => ['required', 'integer', 'exists:propriedades,id'],
        ];
    }
}
