<?php
namespace SqlTark\Clauses;

use SqlTark\Clauses\AbstractClause;

class LimitClause extends AbstractClause
{
    /**
     * @var int $limit
     */
    protected $limit = 0;
    
    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $value)
    {
        $this->limit = $value > 0 ? $value : 0;
    }

    public function hasLimit(): bool
    {
        return $this->limit > 0;
    }

    public function clear(): LimitClause
    {
        $this->limit = 0;
        return $this;
    }

    /**
     * @return LimitClause
     */
    public function clone(): AbstractClause
    {
        /** @var LimitClause */
        $self = parent::clone();

        $self->limit = $this->limit;

        return $self;
    }
}