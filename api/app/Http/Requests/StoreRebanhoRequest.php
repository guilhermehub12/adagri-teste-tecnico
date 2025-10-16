<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRebanhoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'especie' => ['required', 'string', 'max:255'],
            'quantidade' => ['required', 'integer', 'min:1'],
            'finalidade' => ['nullable', 'string', 'max:255'],
            'data_atualizacao' => ['nullable', 'date'],
            'propriedade_id' => ['required', 'integer', 'exists:propriedades,id'],
        ];
    }
}
