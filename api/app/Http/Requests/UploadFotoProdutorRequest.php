<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadFotoProdutorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'foto' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:2048'], // máximo 2MB
        ];
    }

    public function messages(): array
    {
        return [
            'foto.required' => 'A foto é obrigatória.',
            'foto.image' => 'O arquivo deve ser uma imagem.',
            'foto.mimes' => 'A foto deve ser nos formatos: jpeg, jpg ou png.',
            'foto.max' => 'A foto não pode ser maior que 2MB.',
        ];
    }
}
