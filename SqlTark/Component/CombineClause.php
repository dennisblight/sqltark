<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Helper;
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

    public function __clone()
    {
        $this->query = Helper::cloneObject($this->query);
    }
}
