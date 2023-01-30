<?php

namespace YouCanShop\QueryOption;

use YouCanShop\QueryOption\Exceptions\InvalidFilterOperatorException;

class QueryFilter implements Arrayable
{
    public const OPERATOR_EQ = '=';
    public const OPERATOR_IS = 'is';
    public const OPERATOR_IS_NOT = 'is_not';
    public const OPERATOR_NEQ = '!=';
    public const OPERATOR_LT = '<';
    public const OPERATOR_LT_EQ = '<=';
    public const OPERATOR_GT = '>';
    public const OPERATOR_GT_EQ = '>=';
    public const OPERATOR_IN = 'in';

    private string $field;

    private string $operator;

    /** @var mixed */
    private $value;

    /**
     * QueryFilter constructor.
     *
     * @param string $field
     * @param $operator
     * @param null $value
     * @throws InvalidFilterOperatorException
     */
    public function __construct(string $field, $operator, $value = null)
    {
        // fallback to `=` operator when skipped
        if ($value === null) {
            $value = $operator;
            $operator = self::OPERATOR_EQ;
        }

        $this->setOperator($operator);
        $this->field = $field;
        $this->value = $value;
    }

    public function toHumanFormat(): string
    {
        return sprintf("%s %s %s", $this->getField(), $this->getOperator(), $this->getValue());
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     * @throws InvalidFilterOperatorException
     */
    private function setOperator(string $operator): void
    {
        if (!in_array(
            $operator,
            [
                self::OPERATOR_EQ,
                self::OPERATOR_IS,
                self::OPERATOR_IS_NOT,
                self::OPERATOR_NEQ,
                self::OPERATOR_LT,
                self::OPERATOR_LT_EQ,
                self::OPERATOR_GT,
                self::OPERATOR_GT_EQ,
                self::OPERATOR_IN,
            ]
        )) {
            throw new InvalidFilterOperatorException;
        }

        $this->operator = $operator;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            'field' => $this->getField(),
            'operator' => $this->getOperator(),
            'value' => $this->getValue(),
        ];
    }
}
