<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageUsers() ?? false;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:admin,gestor,tecnico,extensionista'],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'O perfil é obrigatório.',
            'role.in' => 'O perfil selecionado é inválido.',
            'email.unique' => 'Este email já está em uso.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'As senhas não conferem.',
        ];
    }
}
