<?php
declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Expressions\BaseExpression;
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

    /**
     * @return ColumnClause
     */
    public function clone(): AbstractComponent
    {
        /** @var ColumnClause */
        $self = parent::clone();

        $self->column = $this->column;

        return $self;
    }
}