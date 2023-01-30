<?php

namespace YouCanShop\QueryOption;

use InvalidArgumentException;

class QuerySearch implements Arrayable
{
    public const SEARCH_TYPE_LIKE = 'like';
    public const SEARCH_TYPE_EQUAL = 'equal';

    private string $term;

    private string $type;

    public function __construct(string $term, ?string $type = self::SEARCH_TYPE_LIKE)
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

    private function setType(?string $type): self
    {
        if ($type === null) {
            $type = self::SEARCH_TYPE_LIKE;
        }

        if (!in_array($type, [self::SEARCH_TYPE_LIKE, self::SEARCH_TYPE_EQUAL])) {
            throw new InvalidArgumentException();
        }

        $this->type = $type;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'term' => $this->getTerm(),
            'type' => $this->getType(),
        ];
    }
}
