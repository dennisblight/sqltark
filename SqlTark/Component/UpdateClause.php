<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Expressions\BaseExpression;
use SqlTark\Helper;
use SqlTark\Query\Query;

class UpdateClause extends AbstractFrom
{
    /**
     * @var (BaseExpression|Query)[] $values
     */
    protected $value;

    /**
     * @return (BaseExpression|Query)[]
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param (BaseExpression|Query)[] $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    public function __clone()
    {
        $this->value = Helper::cloneObject($this->value);
    }
}
