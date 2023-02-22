<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Expressions\BaseExpression;
use SqlTark\Helper;
use SqlTark\Query\Query;

class BetweenCondition extends AbstractCondition
{
    /**
     * @var BaseExpression|Query $column
     */
    protected $column = null;

    /**
     * @var BaseExpression|Query $lower
     */
    protected $lower = null;

    /**
     * @var BaseExpression|Query $higher
     */
    protected $higher = null;

    /**
     * @return BaseExpression|Query
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @param BaseExpression|Query $value
     */
    public function setColumn($value)
    {
        $this->column = $value;
    }

    /**
     * @return BaseExpression|Query
     */
    public function getLower()
    {
        return $this->lower;
    }

    /**
     * @param BaseExpression|Query $value
     */
    public function setLower($value)
    {
        $this->lower = $value;
    }

    /**
     * @return BaseExpression|Query
     */
    public function getHigher()
    {
        return $this->higher;
    }

    /**
     * @param BaseExpression|Query $value
     */
    public function setHigher($value)
    {
        $this->higher = $value;
    }

    public function __clone()
    {
        $this->lower = Helper::cloneObject($this->lower);
        $this->column = Helper::cloneObject($this->column);
        $this->higher = Helper::cloneObject($this->higher);
    }
}
