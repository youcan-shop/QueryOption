<?php

namespace YouCanShop\QueryOption;

class QuerySort implements Arrayable
{
    public const DEFAULT_SORT_FIELD = 'created_at';

    public const SORT_DESC = 'desc';
    public const SORT_ASC = 'asc';

    private string $field;

    private string $direction;

    public function __construct(string $field, string $direction = self::SORT_DESC)
    {
        $this->field = $field;
        $this->setDirection($direction);
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    private function setDirection(string $direction): self
    {
        if (!in_array($direction, [self::SORT_ASC, self::SORT_DESC])) {
            $direction = self::SORT_DESC;
        }

        $this->direction = $direction;

        return $this;
    }

    public function getOppositeDirection(): string
    {
        return $this->getDirection() === self::SORT_DESC ? self::SORT_ASC : self::SORT_DESC;
    }

    public function isField(string $field): bool
    {
        return strtolower($this->getField()) === strtolower($field);
    }

    public function isSortDesc(): bool
    {
        return $this->getDirection() === self::SORT_DESC;
    }

    public function isSortAsc(): bool
    {
        return $this->getDirection() === self::SORT_ASC;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function toArray(): array
    {
        return [
            'sort_field' => $this->getField(),
            'sort_order' => $this->getDirection(),
        ];
    }
}
