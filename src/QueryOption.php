<?php

namespace YouCanShop\QueryOption;

class QueryOption
{
    const MAX_LIMIT = 50;
    const DEFAULT_LIMIT = 10;

    private int $page;

    private int $limit;

    private QuerySearch $search;

    private QueryFilterCollection $filters;

    private QuerySort $sort;

    public function __construct(
        QuerySearch $search,
        QueryFilterCollection $filters,
        QuerySort $sort,
        ?int $page = 1,
        ?int $limit = self::DEFAULT_LIMIT
    ) {
        $this->search = $search;
        $this->filters = $filters;
        $this->sort = $sort;

        $this->setPage($page);
        $this->setLimit($limit);
    }

    public function getPage(): int
    {
        return $this->page;
    }

    private function setPage(?int $page): void
    {
        if ($page === null || $page < 1) {
            $page = 1;
        }

        $this->page = $page;
    }

    public function getSort(): QuerySort
    {
        return $this->sort;
    }

    public function isNotEmpty(): bool
    {
        return $this->isEmpty() === false;
    }

    public function isEmpty(): bool
    {
        return $this->getFilters()->isEmpty() &&
            empty($this->getSearch()->getTerm()) &&
            $this->getLimit() === self::DEFAULT_LIMIT;
    }

    public function getFilters(): QueryFilterCollection
    {
        return $this->filters;
    }

    public function getSearch(): QuerySearch
    {
        return $this->search;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    private function setLimit(?int $limit): self
    {
        if ($limit === null) {
            $limit = self::DEFAULT_LIMIT;
        }

        if ($limit > self::MAX_LIMIT) {
            $limit = self::MAX_LIMIT;
        }

        if ($limit < 0) {
            $limit = self::MAX_LIMIT;
        }

        $this->limit = $limit;

        return $this;
    }

    /**
     * Remove filters from queryOption object if not in the specified list.
     *
     * @param array $filtersNames
     *
     * @return $this
     */
    public function allowedFilters(array $filtersNames): self
    {
        $this->filters = $this->getFilters()->filter(function (QueryFilter $filter) use ($filtersNames) {
            return in_array($filter->getField(), $filtersNames);
        });

        return $this;
    }
}
