<?php

namespace YouCanShop\QueryOption;

use Illuminate\Http\Request as IlluminateRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use YouCanShop\QueryOption\Helpers\Arr;
use YouCanShop\QueryOption\Exceptions\InvalidFilterOperatorException;

class QueryOptionFactory
{
    public static function createFromRequestGlobals(): QueryOption
    {
        return self::createFromArray($_REQUEST);
    }

    public static function createFromArray(array $attributes): QueryOption
    {
        $page = (int)Arr::get($attributes, 'page', 1);
        $limit = (int)Arr::get($attributes, 'limit', QueryOption::DEFAULT_LIMIT);

        $querySearch = new QuerySearch((string)Arr::get($attributes, 'q', ''), Arr::get($attributes, 'search_type'));
        $querySort = new QuerySort(
            (string)Arr::get($attributes, 'sort_field', QuerySort::DEFAULT_SORT_FIELD),
            (string)Arr::get($attributes, 'sort_order', QuerySort::SORT_DESC)
        );

        $queryFilters = new QueryFilterCollection();

        foreach ((array)Arr::get($attributes, 'filters', []) as $filter) {
            if (!Arr::has($filter, ['field', 'value']) || empty($filter['field']) || empty($filter['value'])) {
                continue;
            }

            try {
                $field = (string)Arr::get($filter, 'field', null);
                $operator = Arr::get($filter, 'operator', QueryFilter::OPERATOR_EQ);
                $value = Arr::get($filter, 'value', null);

                $queryFilters->addFilter(new QueryFilter($field, $operator, $value));
            } catch (InvalidFilterOperatorException $e) {
                continue;
            }
        }

        return new QueryOption($querySearch, $queryFilters, $querySort, $page, $limit);
    }

    public static function createFromIlluminateRequest(IlluminateRequest $request): QueryOption
    {
        return self::createFromArray($request->all());
    }

    public static function createFromSymfonyRequest(SymfonyRequest $request): QueryOption
    {
        return self::createFromArray($request->request->all());
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
