<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePropriedadeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'municipio' => ['required', 'string', 'max:255'],
            'uf' => ['required', 'string', 'size:2'],
            'inscricao_estadual' => ['nullable', 'string', 'max:255', Rule::unique('propriedades')->ignore($this->route('propriedade'))],
            'area_total' => ['required', 'numeric', 'min:0'],
            'produtor_id' => ['required', 'integer', 'exists:produtores_rurais,id'],
        ];
    }
}
