<?php

use YouCanShop\QueryOption\QueryFilter;
use YouCanShop\QueryOption\QueryOptionFactory;

test('can create default query option object', function () {
    $queryOption = QueryOptionFactory::createDefault();

    expect($queryOption->getFilters())->toHaveCount(0);
    expect($queryOption->getSearch()->getTerm())->toEqual('');
    expect($queryOption->getSort()->getField())->toEqual('created_at');
    expect($queryOption->isEmpty())->toBeTrue();
});

test('can create query option from request global variables', function () {
    $_REQUEST['q'] = 'term';
    $_REQUEST['sort_field'] = 'price';
    $_REQUEST['sort_order'] = 'asc';
    $_REQUEST['filters'] = [
        [
            'field' => 'created_at',
            'operator' => '>=',
            'value' => '2021-02-22',
        ],
        [
            'field' => 'price',
            'operator' => '>',
            'value' => 120,
        ],
    ];

    $queryOption = QueryOptionFactory::createFromRequestGlobals();

    expect($queryOption->isEmpty())->toBeFalse();
    expect($queryOption->getSearch()->getTerm())->toEqual('term');
    expect($queryOption->getSort()->getField())->toEqual('price');
    expect($queryOption->getSort()->getDirection())->toEqual('asc');
    expect($queryOption->getFilters())->toHaveCount(2);

    foreach ($_REQUEST['filters'] as $filter) {
        $queryOptionFilter = $queryOption->getFilters()->findByName($filter['field']);

        expect($queryOptionFilter)->toBeInstanceOf(QueryFilter::class);
        expect($queryOptionFilter->getField())->toEqual($filter['field']);
        expect($queryOptionFilter->getOperator())->toEqual($filter['operator']);
        expect($queryOptionFilter->getValue())->toEqual($filter['value']);
    }
});
