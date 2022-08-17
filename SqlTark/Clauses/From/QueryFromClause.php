<?php
namespace SqlTark\Clauses\From;

use SqlTark\Query\Query;
use SqlTark\Clauses\AbstractClause;
use SqlTark\Clauses\From\AbstractFrom;

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
        $this->query = clone $value;
    }

    public function getAlias(): string
    {
        return $this->alias ?: $this->query->queryAlias;
    }

    /**
     * @return QueryFromClause
     */
    public function clone(): AbstractClause
    {
        /** @var QueryFromClause */
        $self = parent::clone();

        $self->query = clone $this->query;
        $self->alias = $this->getAlias();

        return $self;
    }
}