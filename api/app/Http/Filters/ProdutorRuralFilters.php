<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProdutorRuralFilters
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function apply(Builder $query): Builder
    {
        foreach ($this->filters as $filter => $value) {
            if (method_exists($this, $filter) && !empty($value)) {
                $this->{$filter}($query, $value);
            }
        }
        return $query;
    }

    public function nome(Builder $query, $value)
    {
        $query->where('nome', 'like', "%{$value}%");
    }

    public function cpf_cnpj(Builder $query, $value)
    {
        $query->where('cpf_cnpj', $value);
    }
    
    public function email(Builder $query, $value)
    {
        $query->where('email', $value);
    }
}
