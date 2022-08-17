<?php
namespace SqlTark\Clauses\Condition;

use SqlTark\Query\Query;
use SqlTark\Clauses\AbstractClause;

class ExistsCondition extends AbstractCondition
{
    /**
     * @var Query $query
     */
    protected $query;

    public function getQuery(): Query
    {
        return $this->query;
    }

    public function setQuery(Query $query)
    {
        $this->query = clone $query;
    }

    /**
     * @return ExistsCondition
     */
    public function clone(): AbstractClause
    {
        /** @var ExistsCondition */
        $self = parent::clone();
        $self->query = clone $this->query;
        return $self;
    }
}