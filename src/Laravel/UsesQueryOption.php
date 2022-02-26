<?php

namespace YouCanShop\QueryOption\Laravel;

use Illuminate\Pipeline\Pipeline;
use YouCanShop\QueryOption\QueryOption;

trait UsesQueryOption
{
    protected function getQueryOptionCriterias(): array
    {
        return [];
    }

    protected function pipeThroughCriterias($query, QueryOption $queryOption)
    {
        /** @var Pipeline $pipeline */
        $pipeline = app(Pipeline::class);

        return $pipeline->through($this->getQueryOptionCriterias())
            ->send([$query, $queryOption])
            ->thenReturn();
    }
}
