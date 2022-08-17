<?php

declare(strict_types=1);

namespace SqlTark\Clauses\Column;

use SqlTark\Query\Query;
use SqlTark\Clauses\AbstractClause;
use SqlTark\Clauses\Column\AbstractColumn;

class QueryColumn extends AbstractColumn
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

    /**
     * @return QueryColumn
     */
    public function clone(): AbstractClause
    {
        /** @var QueryColumn */
        $self = parent::clone();

        $self->query = clone $this->query;

        return $self;
    }
}
