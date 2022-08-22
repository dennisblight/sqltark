<?php

declare(strict_types=1);

namespace SqlTark\Expressions;

class Literal extends BaseExpression
{
    /**
     * @var int|float|string|null|bool|\DateTime
     */
    protected $value;

    /**
     * @return int|float|string|null|bool|\DateTime
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int|float|string|null|bool|\DateTime $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param int|float|string|null|bool|\DateTime $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }
}
