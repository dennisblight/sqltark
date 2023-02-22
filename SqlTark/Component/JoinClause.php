<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Helper;
use SqlTark\Query\Join;

class JoinClause extends AbstractJoin
{
    /**
     * @var Join
     */
    protected $join;

    /**
     * @return Join
     */
    public function getJoin()
    {
        return $this->join;
    }

    /**
     * @param Join $value
     */
    public function setJoin($value)
    {
        $this->join = $value;
    }

    public function __clone()
    {
        $this->join = Helper::cloneObject($this->join);
    }
}
