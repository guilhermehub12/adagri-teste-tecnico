<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class RebanhoFilters
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

    public function especie(Builder $query, $value)
    {
        $query->where('especie', 'like', "%{$value}%");
    }

    public function finalidade(Builder $query, $value)
    {
        $query->where('finalidade', 'like', "%{$value}%");
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
