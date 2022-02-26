<?php

use YouCanShop\QueryOption\QueryFilter;
use YouCanShop\QueryOption\QueryFilterCollection;
use YouCanShop\QueryOption\QueryOption;
use YouCanShop\QueryOption\QuerySearch;
use YouCanShop\QueryOption\QuerySort;

test('query option created with correct values', function () {
    $queryOption = new QueryOption(
        new QuerySearch('term'),
        new QueryFilterCollection(),
        new QuerySort('created_at'),
        2,
        10
    );

    expect($queryOption->getSort())->toBeInstanceOf(QuerySort::class);
    expect($queryOption->getFilters())->toBeInstanceOf(QueryFilterCollection::class);
    expect($queryOption->getSearch())->toBeInstanceOf(QuerySearch::class);
    expect($queryOption->getPage())->toEqual(2);
    expect($queryOption->getLimit())->toEqual(10);
    expect($queryOption->isEmpty())->toBeFalse();
    expect($queryOption->isNotEmpty())->toBeTrue();
});

test('allowed filters remove not allowed filters from filters collection', function () {
    $filtersCollection = (new QueryFilterCollection())
        ->addFilter(new QueryFilter('created_at', '2021-10-02'))
        ->addFilter(new QueryFilter('price', QueryFilter::OPERATOR_GT, 10));

    $queryOption = new QueryOption(
        new QuerySearch('term'),
        $filtersCollection,
        new QuerySort('created_at'),
        2,
        10
    );

    $queryOption->allowedFilters(['price']);

    expect($queryOption->getFilters()->findByName('created_at'))->toBeNull();
    expect($queryOption->getFilters())->toHaveCount(1);
});
