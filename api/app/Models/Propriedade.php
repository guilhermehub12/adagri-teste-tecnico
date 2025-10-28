<?php

namespace App\Models;

use App\Models\Concerns\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Propriedade extends Model
{
    use HasFactory, Filterable;

    protected $table = 'propriedades';

    protected $fillable = [
        'nome',
        'municipio',
        'uf',
        'inscricao_estadual',
        'area_total',
        'produtor_id',
    ];

    protected $casts = [
        'area_total' => 'decimal:2',
    ];

    public function produtor(): BelongsTo
    {
        return $this->belongsTo(ProdutorRural::class, 'produtor_id');
    }

    public function unidadesProducao(): HasMany
    {
        return $this->hasMany(UnidadeProducao::class, 'propriedade_id');
    }

    public function rebanhos(): HasMany
    {
        return $this->hasMany(Rebanho::class, 'propriedade_id');
    }
}
