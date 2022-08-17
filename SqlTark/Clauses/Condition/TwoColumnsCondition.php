<?php
namespace SqlTark\Clauses\Condition;

use SqlTark\Clauses\AbstractClause;

class TwoColumnsCondition extends AbstractCondition
{
    /**
     * @var string $first
     */
    protected $first;
    
    /**
     * @var string $second
     */
    protected $second;
    
    /**
     * @var string $operator
     */
    protected $operator;

    public function getFirst(): string
    {
        return $this->first;
    }

    public function setFirst(string $value)
    {
        $this->first = $value;
    }

    public function getSecond(): string
    {
        return $this->second;
    }

    public function setSecond(string $value)
    {
        $this->second = $value;
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
     * @return TwoColumnsCondition
     */
    public function clone(): AbstractClause
    {
        /** @var TwoColumnsCondition */
        $self = parent::clone();

        $self->first = $this->first;
        $self->second = $this->second;
        $self->operator = $this->operator;

        return $self;
    }
}