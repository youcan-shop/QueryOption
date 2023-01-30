<?php

use YouCanShop\QueryOption\QuerySort;

test('query sort object hold correct values', function () {
    $querySort = new QuerySort('myfield', QuerySort::SORT_ASC);

    expect($querySort->getField())->toEqual('myfield');
    expect($querySort->isField('myfield'))->toBeTrue();

    expect($querySort->getDirection())->toEqual('asc');
    expect($querySort->isSortAsc())->toBeTrue();

    expect($querySort->getOppositeDirection())->toEqual('desc');
    expect($querySort->isSortDesc())->toBeFalse();
});

test('it takes default direction when given the wrong value', function () {
    $querySort = new QuerySort('myfield', 'wrong');

    expect($querySort->getDirection())->toEqual(QuerySort::SORT_DESC);
});

test('it can be casted to array', function () {
    $querySort = new QuerySort('myfield', 'desc');

    expect($querySort->toArray())->toEqual(['sort_field' => 'myfield', 'sort_order' => 'desc']);
});
