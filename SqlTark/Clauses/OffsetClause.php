<?php

declare(strict_types=1);

namespace SqlTark\Clauses;

use SqlTark\Clauses\AbstractClause;

class OffsetClause extends AbstractClause
{
    /**
     * @var int $offset
     */
    protected $offset = 0;

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $value)
    {
        $this->offset = $value > 0 ? $value : 0;
    }

    public function hasOffset(): bool
    {
        return $this->offset > 0;
    }

    public function clear(): OffsetClause
    {
        $this->offset = 0;
        return $this;
    }

    /**
     * @return OffsetClause
     */
    public function clone(): AbstractClause
    {
        /** @var OffsetClause */
        $self = parent::clone();

        $self->offset = $this->offset;

        return $self;
    }
}
