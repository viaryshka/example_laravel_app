<?php

namespace App\Traits;

trait Sortable
{
    public function scopeApplySort($query, array $sort, array $params = [])
    {
        $mainDirection = false;
        $sort = $this->intersectSortKeys($sort);
        foreach ($sort as $key => $value) {
            if (! $mainDirection) {
                $mainDirection = $value;
            }
            if (! $this->setSortBuilder($query, $key, $value, $params)) {
                $query->orderBy($key, $value);
            }
        }
        $mainDirection = $mainDirection ? $mainDirection : 'asc';
        $query->orderBy('id', $mainDirection);

        return $query;
    }

    protected function getSortableKeys(): array
    {
        return is_array($this->sortable)
            ? $this->sortable
            : [];
    }

    protected function intersectSortKeys(array $sort): array
    {
        $availableKeys = $this->getSortableKeys();
        $validSort = [];
        foreach ($sort as $key => $value) {
            if (in_array($key, $availableKeys)) {
                $validSort[$key] = $value;
            }
        }

        return $validSort;
    }

    protected function setSortBuilder($query, $key, $value, $params)
    {
        return false;
    }
}
