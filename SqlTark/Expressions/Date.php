<?php

declare(strict_types=1);

namespace SqlTark\Expressions;

class Date extends BaseExpression
{
    /**
     * @var string|Query
     */
    protected $value;

    /**
     * @return string|Query
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string|Query $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param string|Query $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }
}
