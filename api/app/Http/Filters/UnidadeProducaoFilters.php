<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class UnidadeProducaoFilters
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function apply(Builder $query): Builder
    {
        foreach ($this->filters as $filter => $value) {
            if (method_exists($this, $filter)) {
                $this->{$filter}($query, $value);
            }
        }
        return $query;
    }

    public function nome_cultura(Builder $query, $value)
    {
        $query->where('nome_cultura', 'like', "%{$value}%");
    }

    public function area_total_ha(Builder $query, $value)
    {
        $query->where('area_total_ha', $value);
    }

    public function coordenadas_geograficas(Builder $query, $value)
    {
        $query->where('coordenadas_geograficas', 'like', "%{$value}%");
    }

    public function propriedade_id(Builder $query, $value)
    {
        $query->where('propriedade_id', $value);
    }

    public function created_at(Builder $query, $value)
    {
        $query->whereDate('created_at', $value);
    }
}
