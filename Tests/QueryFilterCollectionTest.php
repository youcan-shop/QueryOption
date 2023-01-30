<?php

use YouCanShop\QueryOption\QueryFilter;
use YouCanShop\QueryOption\QueryFilterCollection;

test('I can add filter by params', function () {
    $filterCollection = new QueryFilterCollection();
    $filterCollection->addFilterParams('myfield', 'myvalue');

    $queryFilter = $filterCollection->findByName('myfield');

    expect($queryFilter)->toBeInstanceOf(QueryFilter::class);
    expect($filterCollection)->toHaveCount(1);
});

test('I can add filter by object', function () {
    $filterCollection = new QueryFilterCollection();
    $filterCollection->addFilter(new QueryFilter('myfield', 'myvalue'));

    $queryFilter = $filterCollection->findByName('myfield');

    expect($queryFilter)->toBeInstanceOf(QueryFilter::class);
    expect($filterCollection)->toHaveCount(1);
});

test('I can find query filter by name', function () {
    $filterCollection = new QueryFilterCollection();
    $filterCollection->addFilterParams('myfield', 'myvalue');

    $queryFilter = $filterCollection->findByName('myfield');

    expect($queryFilter)->toBeInstanceOf(QueryFilter::class);
    expect($queryFilter->getField())->toEqual('myfield');
});

test('when I add a filter that already exist by the same name, the old one is deleted', function () {
    $filterCollection = new QueryFilterCollection();
    $filterCollection->addFilterParams('myfield', 'myvalue1');

    $filterCollection->addFilterParams('myfield', 'myvalue2');

    $queryFilter = $filterCollection->findByName('myfield');

    expect($queryFilter)->toBeInstanceOf(QueryFilter::class);
    expect($queryFilter->getValue())->toEqual('myvalue2');
});

test('it can be casted to array', function () {
    $filterCollection = new QueryFilterCollection();
    $filterCollection->addFilterParams('myfield1', 'myvalue1');
    $filterCollection->addFilterParams('myfield2', 'myvalue2');

    $expectedArray = [
        ['field' => 'myfield1', 'operator' => '=', 'value' => 'myvalue1'],
        ['field' => 'myfield2', 'operator' => '=', 'value' => 'myvalue2'],
    ];

    foreach ($filterCollection->toArray() as $key => $filterArray) {
        expect($filterArray)->toMatchArray($expectedArray[$key]);
    }
});
