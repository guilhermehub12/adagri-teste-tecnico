<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class PropriedadeFilters
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

    public function nome(Builder $query, $value)
    {
        $query->where('nome', 'like', "%{$value}%");
    }

    public function municipio(Builder $query, $value)
    {
        $query->where('municipio', 'like', "%{$value}%");
    }

    public function uf(Builder $query, $value)
    {
        $query->where('uf', $value);
    }

    public function inscricao_estadual(Builder $query, $value)
    {
        $query->where('inscricao_estadual', $value);
    }

    public function produtor_id(Builder $query, $value)
    {
        $query->where('produtor_id', $value);
    }

    public function created_at(Builder $query, $value)
    {
        $query->whereDate('created_at', $value);
    }
}
