<?php

namespace YouCanShop\QueryOption\Laravel;

use Illuminate\Support\Facades\App;
use YouCanShop\QueryOption\QueryOption;

abstract class AbstractQueryOptionCriteria
{
    use UsesQueryOption;

    abstract public function getFilterName(): string;

    public function getQueryOption(): QueryOption
    {
        return App::make(QueryOption::class);
    }
}
