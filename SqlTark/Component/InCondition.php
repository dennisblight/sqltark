<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SplFixedArray;
use SqlTark\Expressions\BaseExpression;
use SqlTark\Helper;
use SqlTark\Query\Query;

class InCondition extends AbstractCondition
{
    /**
     * @var BaseExpression|Query $column
     */
    protected $column;

    /**
     * @var SplFixedArray<BaseExpression>|Query $values
     */
    protected $values;

    /**
     * @return BaseExpression|Query $column
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
     * @return SplFixedArray<BaseExpression>|Query
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param SplFixedArray<BaseExpression>|Query $value
     */
    public function setValues($value)
    {
        $this->values = $value;
    }

    public function __clone()
    {
        $this->column = Helper::cloneObject($this->column);
        $this->values = Helper::cloneObject($this->values);
    }
}
