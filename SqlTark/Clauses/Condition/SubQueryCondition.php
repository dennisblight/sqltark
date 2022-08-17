<?php
namespace SqlTark\Clauses\Condition;

use SqlTark\Query;
use SqlTark\Clauses\AbstractClause;

class SubQueryCondition extends AbstractCondition
{
    /**
     * @var Query $query
     */
    protected $query;
    
    /**
     * @var mixed $value
     */
    protected $value;
    
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

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value)
    {
        $this->value = $value;
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
     * @return SubQueryCondition
     */
    public function clone(): AbstractClause
    {
        /** @var SubQueryCondition */
        $self = parent::clone();

        $self->query = clone $this->query;
        $self->value = $this->value;
        $self->operator = $this->operator;

        return $self;
    }
}