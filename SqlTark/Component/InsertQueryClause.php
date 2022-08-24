<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Query\Query;

class InsertQueryClause extends AbstractInsert
{
    /**
     * @var Query $query
     */
    protected $query;

    /**
     * @var ?iterable<string> $columns
     */
    protected $columns;

    public function getQuery(): Query
    {
        return $this->query;
    }

    public function setQuery(Query $value)
    {
        $this->query = $value;
    }

    public function getColumns(): ?iterable
    {
        return $this->columns;
    }

    public function setColumns(?iterable $value)
    {
        $this->columns = $value;
    }

    /**
     * @return static Clone of current object
     */
    public function clone()
    {
        $self = parent::clone();

        $self->query = clone $this->query;
        $self->columns = $this->columns;

        return $self;
    }
}
