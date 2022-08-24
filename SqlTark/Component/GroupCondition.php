<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Query\Condition;

class GroupCondition extends AbstractCondition
{
    /**
     * @var Condition $condition
     */
    protected $condition;

    public function getCondition(): Condition
    {
        return $this->condition;
    }

    public function setCondition(Condition $value)
    {
        $this->condition = $value;
    }

    /**
     * @return static Clone of current object
     */
    public function clone()
    {
        /** @var GroupCondition */
        $self = parent::clone();

        $self->condition = clone $this->condition;

        return $self;
    }
}
