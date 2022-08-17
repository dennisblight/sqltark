<?php

declare(strict_types=1);

namespace SqlTark\Clauses\Condition;

use SqlTark\Clauses\AbstractClause;

class BooleanCondition extends AbstractCondition
{
    /**
     * @var string $column
     */
    protected $column;

    /**
     * @var bool $value
     */
    protected $value;

    public function getColumn(): string
    {
        return $this->column;
    }

    public function setColumn(string $value)
    {
        $this->column = $value;
    }

    public function getValue(): bool
    {
        return $this->value;
    }

    public function setValue(bool $value)
    {
        $this->value = $value;
    }

    /**
     * @return BooleanCondition
     */
    public function clone(): AbstractClause
    {
        /** @var BooleanCondition */
        $self = parent::clone();

        $self->column = $this->column;
        $self->value = $this->value;

        return $self;
    }
}
