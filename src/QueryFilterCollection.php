<?php

namespace YouCanShop\QueryOption;

use Illuminate\Support\Collection;

class QueryFilterCollection extends Collection
{
    public function deleteByName(string $name): self
    {
        $this->each(
            function (QueryFilter $queryFilter, $index) use ($name) {
                if ($queryFilter->getField() === $name) {
                    $this->forget($index);
                }
            }
        );

        return $this;
    }

    /**
     * @param string $field
     * @param string|mixed|null $operator
     * @param mixed|null $value
     *
     * @return $this
     */
    public function addFilterParams(string $field, $operator, $value = null): self
    {
        $this->addFilter(new QueryFilter($field, $operator, $value));

        return $this;
    }

    /**
     * @param QueryFilter $queryFilter
     *
     * @return $this
     */
    public function addFilter(QueryFilter $queryFilter): self
    {
        if ($this->hasFilter($queryFilter->getField())) {
            $this->deleteByName($queryFilter->getField());
        }

        $this->push($queryFilter);

        return $this;
    }

    public function hasFilter(string $name): bool
    {
        return $this->findByName($name) instanceof QueryFilter;
    }

    public function findByName(string $name): ?QueryFilter
    {
        return $this->first(
            function (QueryFilter $queryFilter) use ($name) {
                return $queryFilter->getField() === $name;
            }
        );
    }
}
