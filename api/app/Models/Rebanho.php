<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rebanho extends Model
{
    protected $table = 'rebanhos';

    protected $fillable = [
        'especie',
        'quantidade',
        'finalidade',
        'data_atualizacao',
        'propriedade_id',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'data_atualizacao' => 'datetime',
    ];

    public function propriedade(): BelongsTo
    {
        return $this->belongsTo(Propriedade::class, 'propriedade_id');
    }
}
