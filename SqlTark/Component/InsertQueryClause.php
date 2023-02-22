<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Helper;
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

    public function __clone()
    {
        $this->query = Helper::cloneObject($this->query);
        $this->columns = Helper::cloneObject($this->columns);
    }
}
