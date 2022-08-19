<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Query\Query;
use SqlTark\Component\AbstractFrom;

class QueryFromClause extends AbstractFrom
{
    /**
     * @var Query $query
     */
    protected $query;

    public function getQuery(): Query
    {
        return $this->query;
    }

    public function setQuery(Query $value)
    {
        $this->query = $value;
    }

    public function getAlias(): string
    {
        return $this->alias ?: $this->query->queryAlias;
    }

    /**
     * @return QueryFromClause
     */
    public function clone(): AbstractComponent
    {
        /** @var QueryFromClause */
        $self = parent::clone();

        $self->query = clone $this->query;
        $self->alias = $this->getAlias();

        return $self;
    }
}
