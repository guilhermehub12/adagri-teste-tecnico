<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * Aplica todos os filtros da requisição ao Query Builder.
     *
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $filterClass = 'App\\Http\\Filters\\' . class_basename($this) . 'Filters';

        if (class_exists($filterClass)) {
            $filterInstance = new $filterClass($filters);
            return $filterInstance->apply($query);
        }

        return $query;
    }
}
