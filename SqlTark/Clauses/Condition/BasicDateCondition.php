<?php

declare(strict_types=1);

namespace SqlTark\Clauses\Condition;

use SqlTark\Clauses\AbstractClause;

class BasicDateCondition extends BasicCondition
{
    /**
     * @var string $part
     */
    protected $part;

    public function getPart(): string
    {
        return $this->part;
    }

    public function setPart(string $value)
    {
        $this->part = $value;
    }

    /**
     * @return BasicDateCondition
     */
    public function clone(): AbstractClause
    {
        /** @var BasicDateCondition */
        $self = parent::clone();

        $self->part = $this->part;

        return $self;
    }
}
