<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    /**
     * Scope search by keywords.
     * @param  Builder  $query
     * @param $keywords
     * @param  array  $fields
     * @param  array  $params
     * @return mixed
     */
    public function scopeKeywordsSearch(Builder $query, $keywords, $fields = [], $params = [])
    {
        $keywords = trim($keywords);
        $keywords = '%'.$keywords.'%';
        $fields = $this->intersectSearchFields($fields);
        $query->where(function ($query) use ($keywords, $fields, $params) {
            $or = false;
            foreach ($fields as $field) {
                if (! $this->setSearchBuilder($query, $field, $keywords, $or, $params)) {
                    $query->likeOrWhere($field, $keywords, $or);
                }
                $or = true;
            }
        });

        return $query;
    }

    /**
     * @param  Builder  $query
     * @param $field
     * @param $keywords
     * @param  false  $or
     * @return Builder
     */
    public function scopeLikeOrWhere(Builder $query, $field, $keywords, $or = false)
    {
        if ($or) {
            $query->orWhere($field, 'LIKE', $keywords);
        } else {
            $query->where($field, 'LIKE', $keywords);
        }

        return $query;
    }

    /**
     * @param  Builder  $query
     * @param $field
     * @param $prop
     * @param $keywords
     * @param  false  $or
     * @return Builder
     */
    public function scopeJsonLikeOrWhere(Builder $query, $field, $prop, $keywords, $or = false)
    {
        if ($or) {
            $query->orWhereRaw("json_unquote(UPPER(json_extract(`$field`, '$.\"$prop\"'))) LIKE ?",
                [strtoupper($keywords)]);
        } else {
            $query->whereRaw("json_unquote(UPPER(json_extract(`$field`, '$.\"$prop\"'))) LIKE ?",
                [strtoupper($keywords)]);
        }

        return $query;
    }

    /**
     * @param  Builder  $query
     * @param $scopeName
     * @param $keywords
     * @param  false  $or
     * @return Builder
     */
    public function scopeScopeOrWhere(Builder $query, $scopeName, $keywords, $or = false)
    {
        if ($or) {
            $query->orWhere->{$scopeName}($keywords);
        } else {
            $query->{$scopeName}($keywords);
        }

        return $query;
    }

    protected function getSearchFields(): array
    {
        return is_array($this->searchFields)
            ? $this->searchFields
            : [];
    }

    protected function intersectSearchFields(array $fields = []): array
    {
        $availableKeys = $this->getSearchFields();
        $validFields = [];
        foreach ($fields as $key => $value) {
            if (in_array($value, $availableKeys)) {
                $validFields[$key] = $value;
            }
        }
        if (! count($validFields)) {
            return $availableKeys;
        }

        return $validFields;
    }

    protected function setSearchBuilder(Builder $query, $field, $keywords, $or, $params)
    {
        return false;
    }
}
