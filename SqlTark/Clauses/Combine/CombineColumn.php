<?php

declare(strict_types=1);

namespace SqlTark\Clauses\Combine;

use SqlTark\Query\Query;
use SqlTark\Clauses\AbstractClause;

class CombineColumn extends AbstractCombine
{
    /**
     * @var Query $query
     */
    protected $query;

    /**
     * @var string $operator
     */
    protected $operator;

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
        $this->query = clone $value;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function setOperator(string $value)
    {
        $this->operator = $value;
    }

    public function getAll(): bool
    {
        return $this->all;
    }

    public function setAll(bool $value)
    {
        $this->all = $value;
    }

    /**
     * @return CombineColumn
     */
    public function clone(): AbstractClause
    {
        /** @var CombineColumn */
        $self = parent::clone();

        $self->query = clone $this->expression;
        $self->operator = $this->operator;
        $self->all = $this->all;

        return $self;
    }
}
