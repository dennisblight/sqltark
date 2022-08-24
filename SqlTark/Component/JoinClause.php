<?php

declare(strict_types=1);

namespace SqlTark\Component;

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

    /**
     * @return static Clone of current object
     */
    public function clone()
    {
        $self = parent::clone();

        $self->join = clone $this->join;

        return $self;
    }
}
