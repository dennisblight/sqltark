<?php
declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Expressions\BaseExpression;
use SqlTark\Helper;
use SqlTark\Query\Query;

class OrderClause extends AbstractOrder
{
    /**
     * @var BaseExpression|Query
     */
    protected $column;

    /**
     * @var bool
     */
    protected $ascending;

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
     * @return bool
     */
    public function isAscending(): bool
    {
        return $this->ascending;
    }

    public function setAscending(bool $value)
    {
        $this->ascending = $value;
    }

    public function __clone()
    {
        $this->column = Helper::cloneObject($this->column);
    }
}