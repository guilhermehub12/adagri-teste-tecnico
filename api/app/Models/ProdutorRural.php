<?php

namespace App\Models;

use App\Models\Concerns\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ProdutorRural extends Model
{
    use HasFactory, Filterable;

    protected $table = 'produtores_rurais';

    protected $fillable = [
        'nome',
        'cpf_cnpj',
        'telefone',
        'email',
        'endereco',
        'data_cadastro',
        'foto',
    ];

    protected $casts = [
        'data_cadastro' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function (ProdutorRural $produtor) {
            if ($produtor->foto) {
                Storage::disk('public')->delete('produtores/' . basename($produtor->foto));
            }
        });
    }

    public function propriedades(): HasMany
    {
        return $this->hasMany(Propriedade::class, 'produtor_id');
    }
}
