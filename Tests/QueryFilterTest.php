<?php

use YouCanShop\QueryOption\Exceptions\InvalidFilterOperatorException;
use YouCanShop\QueryOption\QueryFilter;

test('query filter object hold correct values', function () {
    $queryFilter = new QueryFilter('myfield', QueryFilter::OPERATOR_EQ, 'myvalue');

    expect($queryFilter->getField())->toEqual('myfield');
    expect($queryFilter->getValue())->toEqual('myvalue');
    expect($queryFilter->getOperator())->toEqual('=');
    expect($queryFilter->toHumanFormat())->toEqual('myfield = myvalue');
});

test('query filter fallback to default operator when skipping param', function () {
    $queryFilter = new QueryFilter('myfield',  'myvalue');

    expect($queryFilter->getField())->toEqual('myfield');
    expect($queryFilter->getValue())->toEqual('myvalue');
    expect($queryFilter->getOperator())->toEqual('=');
});

test('it throws exception on invalid operator', function () {
    new QueryFilter('myfield', 'wrong', 'myvalue');
})->throws(InvalidFilterOperatorException::class);

test('it can be casted to array', function () {
    $queryFilter = new QueryFilter('myfield', QueryFilter::OPERATOR_EQ, 'myvalue');

    expect($queryFilter->toArray())->toEqual(['field' => 'myfield', 'operator' => '=', 'value' => 'myvalue']);
});
