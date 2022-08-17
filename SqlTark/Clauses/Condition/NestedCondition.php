<?php

declare(strict_types=1);

namespace SqlTark\Clauses\Condition;

use SqlTark\Query\Query;
use SqlTark\Clauses\AbstractClause;

class NestedCondition extends AbstractCondition
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

    public function clone(): AbstractClause
    {
        $self = parent::clone();
        $self->query = clone $this->query;
        return $self;
    }
}
