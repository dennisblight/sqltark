<?php
namespace SqlTark\Clauses\Condition;

use SqlTark\Clauses\AbstractClause;

class NullCondition extends AbstractCondition
{
    /**
     * @var string $column
     */
    protected $column;

    public function getColumn(): string
    {
        return $this->column;
    }

    public function setColumn(string $value)
    {
        $this->column = $value;
    }

    /** 
     * @return NullCondition
     */
    public function clone(): AbstractClause
    {
        /** @var NullCondition */
        $self = parent::clone();

        $self->column = $this->column;

        return $self;
    }
}