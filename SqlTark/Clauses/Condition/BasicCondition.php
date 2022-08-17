<?php

declare(strict_types=1);

namespace SqlTark\Clauses\Condition;

use SqlTark\Clauses\AbstractClause;
use SqlTark\Clauses\Condition\AbstractCondition;

class BasicCondition extends AbstractCondition
{
    /**
     * @var string $column
     */
    protected $column = false;

    /**
     * @var string $operator
     */
    protected $operator = false;

    /**
     * @var mixed $value
     */
    protected $value = false;

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

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value)
    {
        $this->value = $value;
    }

    /**
     * @return BasicCondition
     */
    public function clone(): AbstractClause
    {
        /** @var BasicCondition */
        $self = parent::clone();

        $self->column = $this->column;
        $self->operator = $this->operator;
        $self->value = $this->value;

        return $self;
    }
}
