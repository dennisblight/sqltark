<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Query\Query;

class GroupCondition extends AbstractCondition
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

    /**
     * @return GroupCondition
     */
    public function clone(): AbstractComponent
    {
        /** @var GroupCondition */
        $self = parent::clone();

        $self->query = clone $this->query;

        return $self;
    }
}
