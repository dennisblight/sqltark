<?php
declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Expressions\BaseExpression;
use SqlTark\Helper;
use SqlTark\Query\Query;

class ColumnClause extends AbstractColumn
{
    /**
     * @var BaseExpression|Query
     */
    protected $column;

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

    public function __clone()
    {
        $this->column = Helper::cloneObject($this->column);
    }
}