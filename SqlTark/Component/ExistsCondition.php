<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Helper;
use SqlTark\Query\Query;

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

    public function setQuery(Query $value)
    {
        $this->query = $value;
    }

    public function __clone()
    {
        $this->query = Helper::cloneObject($this->query);
    }
}
