<?php
namespace SqlTark\Clauses\Condition;

use SqlTark\Clauses\AbstractClause;

class BetweenCondition extends AbstractCondition
{
    /**
     * @var string $column
     */
    protected $column;
    
    /**
     * @var mixed $lower
     */
    protected $lower;
    
    /**
     * @var mixed $higher
     */
    protected $higher;

    public function getColumn(): string
    {
        return $this->column;
    }

    public function setColumn(string $value)
    {
        $this->column = $value;
    }

    public function getLower(): mixed
    {
        return $this->lower;
    }

    public function setLower(mixed $value)
    {
        $this->lower = $value;
    }

    public function getHigher(): mixed
    {
        return $this->higher;
    }

    public function setHigher(mixed $value)
    {
        $this->higher = $value;
    }

    /**
     * @return BetweenCondition
     */
    public function clone(): AbstractClause
    {
        /** @var BetweenCondition */
        $self = parent::clone();
        $self->column = $this->column;
        $self->lower = $this->lower;
        $self->higher = $this->higher;
        return $self;
    }
}