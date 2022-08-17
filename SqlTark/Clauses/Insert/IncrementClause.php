<?php
namespace SqlTark\Clauses\Insert;

use SqlTark\Clauses\AbstractClause;

class IncrementClause extends InsertClause
{
    /**
     * @var string $column
     */
    protected $column;

    /**
     * @var int|float $value
     */
    protected $value = 1;

    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * @return int|float
     */
    public function getValue()
    {
        return $this->value;
    }

    public function setColumn(string $value)
    {
        $this->column = $value;
    }

    /**
     * @param int|float $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return IncrementClause
     */
    public function clone(): AbstractClause
    {
        /** @var IncrementClause */
        $self = parent::clone();

        $self->column = $this->column;
        $self->value = $this->value;

        return $self;
    }
}