<?php
namespace SqlTark\Clauses\Condition;

use SqlTark\Clauses\AbstractClause;

class InCondition extends AbstractCondition
{
    /**
     * @var string $column
     */
    protected $column;
    
    /**
     * @var iterable $values
     */
    protected $values;

    public function getColumn(): string
    {
        return $this->column;
    }

    public function setColumn(string $value)
    {
        $this->column = $value;
    }

    public function getValues(): iterable
    {
        return $this->values;
    }

    public function setValues(iterable $value)
    {
        $this->values = $value;
    }

    /**
     * @return InCondition
     */
    public function clone(): AbstractClause
    {
        /** @var InCondition */
        $self = parent::clone();

        $self->column = $this->column;
        $self->values = $this->values;

        return $self;
    }
}