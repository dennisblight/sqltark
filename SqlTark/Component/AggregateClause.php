<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Expressions\BaseExpression;
use SqlTark\Query\Query;

class AggregateClause extends AbstractComponent
{
    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var 
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
     * @param Query|BaseExpression
     */
    public function setColumn($value)
    {
        $this->column = $value;
    }

    public function clone()
    {
        $self = parent::clone();

        $self->type = $this->type;
        
        $self->column = $this->column instanceof Query
            ? clone $this->column
            : $this->column;

        return $self;
    }
}