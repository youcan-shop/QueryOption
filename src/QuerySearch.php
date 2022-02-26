<?php

namespace YouCanShop\QueryOption;

use InvalidArgumentException;

class QuerySearch
{
    public const SEARCH_TYPE_LIKE = 1;
    public const SEARCH_TYPE_EQUAL = 2;

    private string $term;

    private string $type;

    public function __construct(string $term, string $type = self::SEARCH_TYPE_LIKE)
    {
        $this->term = $term;
        $this->setType($type);
    }

    public function getTerm(): string
    {
        return $this->term;
    }

    public function getType(): string
    {
        return $this->type;
    }

    private function setType(string $type): self
    {
        if (!in_array($type, [self::SEARCH_TYPE_LIKE, self::SEARCH_TYPE_EQUAL])) {
            throw new InvalidArgumentException();
        }

        $this->type = $type;

        return $this;
    }
}
