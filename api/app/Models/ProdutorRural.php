<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProdutorRural extends Model
{
    protected $table = 'produtores_rurais';

    protected $fillable = [
        'nome',
        'cpf_cnpj',
        'telefone',
        'email',
        'endereco',
        'data_cadastro',
    ];

    protected $casts = [
        'data_cadastro' => 'datetime',
    ];

    public function propriedades(): HasMany
    {
        return $this->hasMany(Propriedade::class, 'produtor_id');
    }
}
