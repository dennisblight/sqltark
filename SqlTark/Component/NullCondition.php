<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Expressions\BaseExpression;
use SqlTark\Query\Query;

class NullCondition extends AbstractCondition
{
    /**
     * @var BaseExpression|Query $column
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

    /**
     * @return FromClause
     */
    public function clone(): AbstractComponent
    {
        /** @var FromClause */
        $self = parent::clone();

        $self->column = $this->column;

        return $self;
    }
}
