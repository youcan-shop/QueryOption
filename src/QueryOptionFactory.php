<?php

namespace YouCanShop\QueryOption;

use Illuminate\Support\Arr;
use YouCanShop\QueryOption\Exceptions\InvalidFilterOperatorException;

class QueryOptionFactory
{
    public static function createFromRequestGlobals(): QueryOption
    {
        $page = (int)Arr::get($_REQUEST, 'page', 1);
        $limit = (int)Arr::get($_REQUEST, 'limit', QueryOption::DEFAULT_LIMIT);

        $querySearch = new QuerySearch((string)Arr::get($_REQUEST, 'q', ''));
        $querySort = new QuerySort(
            (string)Arr::get($_REQUEST, 'sort_field', QuerySort::DEFAULT_SORT_FIELD),
            (string)Arr::get($_REQUEST, 'sort_order', QuerySort::SORT_DESC)
        );

        $queryFilters = new QueryFilterCollection();

        foreach ((array)Arr::get($_REQUEST, 'filters', []) as $filter) {
            if (!Arr::has($filter, ['field', 'value']) || empty($filter['field']) || empty($filter['value'])) {
                continue;
            }

            try {
                $field = (string)Arr::get($filter, 'field', null);
                $operator = Arr::get($filter, 'operator', QueryFilter::OPERATOR_EQ);
                $value = Arr::get($filter, 'value', null);

                $queryFilters->push(new QueryFilter($field, $operator, $value));
            } catch (InvalidFilterOperatorException $e) {
                continue;
            }
        }

        return new QueryOption($querySearch, $queryFilters, $querySort, $page, $limit);
    }

    public static function createDefault(): QueryOption
    {
        $querySearch = new QuerySearch('');
        $querySort = new QuerySort(
            QuerySort::DEFAULT_SORT_FIELD,
            QuerySort::SORT_DESC
        );

        $queryFilters = new QueryFilterCollection();

        return new QueryOption($querySearch, $queryFilters, $querySort, 1, QueryOption::DEFAULT_LIMIT);
    }
}
