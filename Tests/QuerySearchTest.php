<?php

use YouCanShop\QueryOption\QuerySearch;

test('query search object hold correct values', function () {
    $querySearch = new QuerySearch('test', QuerySearch::SEARCH_TYPE_LIKE);

    expect($querySearch->getTerm())->toEqual('test');
    expect($querySearch->getType())->toEqual(QuerySearch::SEARCH_TYPE_LIKE);
});

test('it throws exception when given invalid search type', function () {
    new QuerySearch('test', 10);
})->throws(InvalidArgumentException::class);
