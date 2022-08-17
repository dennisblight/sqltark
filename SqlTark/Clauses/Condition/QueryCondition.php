<?php
namespace SqlTark\Clauses\Condition;

use SqlTark\Query\Query;
use SqlTark\Clauses\AbstractClause;

class QueryCondition extends AbstractCondition
{
    /**
     * @var Query $query
     */
    protected $query;
    
    /**
     * @var string $column
     */
    protected $column;
    
    /**
     * @var string $operator
     */
    protected $operator;

    public function getQuery(): Query
    {
        return $this->query;
    }

    public function setQuery(Query $value)
    {
        $this->query = clone $value;
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function setColumn(string $value)
    {
        $this->column = $value;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function setOperator(string $value)
    {
        $this->operator = $value;
    }

    /**
     * @return QueryCondition
     */
    public function clone(): AbstractClause
    {
        /** @var QueryCondition */
        $self = parent::clone();

        $self->query = clone $this->query;
        $self->column = $this->column;
        $self->operator = $this->operator;

        return $self;
    }
}