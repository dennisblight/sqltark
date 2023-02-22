<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Expressions\BaseExpression;
use SqlTark\Helper;
use SqlTark\Query\Query;

class AggregateClause extends AbstractComponent
{
    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var Query|BaseExpression
     */
    protected $column;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $value)
    {
        $this->type = $value;
    }

    /**
     * @return Query|BaseExpression
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @param Query|BaseExpression $value
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