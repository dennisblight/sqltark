<?php

declare(strict_types=1);

namespace SqlTark\Clauses\OrderBy;

use SqlTark\Clauses\AbstractClause;

class OrderBy extends AbstractOrderBy
{
    /**
     * @var string $column
     */
    protected $column;

    /**
     * @var bool $ascending
     */
    protected $ascending = true;

    public function getColumn(): string
    {
        return $this->column;
    }

    public function setColumn(string $value)
    {
        $this->column = $value;
    }

    public function getAscending(): bool
    {
        return $this->ascending;
    }

    public function setAscending(bool $value)
    {
        $this->ascending = $value;
    }

    /**
     * @return OrderBy
     */
    public function clone(): AbstractClause
    {
        /** @var OrderBy */
        $self = parent::clone();

        $self->column = $this->column;
        $self->ascending = $this->ascending;

        return $self;
    }
}
