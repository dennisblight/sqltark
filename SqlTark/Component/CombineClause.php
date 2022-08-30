<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Query\Query;

class CombineClause extends AbstractComponent
{
    /**
     * @var Query $query
     */
    protected $query;

    /**
     * @var int $operation
     */
    protected $operation;

    /**
     * @var bool $all
     */
    protected $all = false;

    public function getQuery(): Query
    {
        return $this->query;
    }

    public function setQuery(Query $value)
    {
        $this->query = $value;
    }

    public function getOperation(): int
    {
        return $this->operation;
    }

    public function setOperation(int $value)
    {
        $this->operation = $value;
    }

    public function isAll(): bool
    {
        return $this->all;
    }

    public function setAll(bool $value)
    {
        $this->all = $value;
    }

    /**
     * @return static Clone of current object
     */
    public function clone()
    {
        $self = parent::clone();

        $self->query = clone $this->query;
        $self->value = $this->value;
        $self->all = $this->all;

        return $self;
    }
}
