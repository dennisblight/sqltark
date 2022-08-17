<?php
namespace SqlTark\Clauses\Condition;

use SqlTark\Query\Query;
use SqlTark\Clauses\AbstractClause;

class InQueryCondition extends AbstractCondition
{
    /**
     * @var string $column
     */
    protected $column;
    
    /**
     * @var Query $query
     */
    protected $query;

    public function getColumn(): string
    {
        return $this->column;
    }

    public function setColumn(string $value)
    {
        $this->column = $value;
    }

    public function getQuery(): Query
    {
        return $this->query;
    }

    public function setQuery(Query $value)
    {
        $this->query = clone $value;
    }

    public function clone(): AbstractClause
    {
        $self = parent::clone();
        $self->column = $this->column;
        $self->query = clone $this->query;
        return $self;
    }
}