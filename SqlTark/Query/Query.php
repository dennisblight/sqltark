<?php

declare(strict_types=1);

namespace SqlTark\Query;

class Query extends BaseQuery
{
    public function newQuery(): BaseQuery
    {
        return $this;
    }

    public function as(string $alias)
    {

    }
}
