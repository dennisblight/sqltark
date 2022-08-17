<?php
namespace SqlTark\Clauses\Join;

use SqlTark\Query\Join;
use SqlTark\Clauses\AbstractClause;

class BaseJoin extends AbstractJoin
{
    /**
     * @var Join $join
     */
    protected $join;

    public function getJoin(): Join
    {
        return $this->join;
    }

    public function setJoin(Join $value)
    {
        $this->join = clone $value;
    }

    /**
     * @return BaseJoin
     */
    public function clone(): AbstractClause
    {
        /** @var BaseJoin */
        $self = parent::clone();

        $self->join = clone $this->join;

        return $self;
    }
}