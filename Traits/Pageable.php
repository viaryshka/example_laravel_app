<?php

namespace App\Traits;

trait Pageable
{
    /**
     * @param  $builder
     * @param  int  $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function paginateResults($builder, int $perPage = 0)
    {
        if ($perPage > 0) {
            return $builder->paginate($perPage);
        }

        return $builder->get();
    }
}
