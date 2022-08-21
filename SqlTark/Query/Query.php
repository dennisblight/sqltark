<?php

declare(strict_types=1);

namespace SqlTark\Query;

use SqlTark\Query\BaseQuery;
use SqlTark\Query\Traits\OrderTrait;
use SqlTark\Query\Traits\PagingTrait;
use SqlTark\Query\Traits\SelectTrait;
use SqlTark\Query\Traits\ConditionTrait;
use SqlTark\Query\Traits\FromTrait;
use SqlTark\Query\Interfaces\ConditionInterface;
use SqlTark\Query\Interfaces\HavingInterface;
use SqlTark\Query\Interfaces\QueryInterface;
use SqlTark\Query\Traits\AggregateTrait;
use SqlTark\Query\Traits\GroupByTrait;
use SqlTark\Query\Traits\HavingTrait;
use SqlTark\Query\Traits\JoinTrait;

class Query extends BaseQuery implements ConditionInterface, HavingInterface
{
    use FromTrait,
        SelectTrait,
        AggregateTrait,
        JoinTrait,
        ConditionTrait,
        OrderTrait,
        PagingTrait,
        GroupByTrait,
        HavingTrait;

    public function __construct($table = null)
    {
        if (!is_null($table)) {
            $this->from($table);
        }
    }

    public function clone(): QueryInterface
    {
        $self = parent::clone();

        $self->alias = $this->alias;
        $self->distinct = $this->distinct;
        $self->orFlag = $this->orFlag;
        $self->notFlag = $this->notFlag;
        $self->havingFlag = $this->havingFlag;

        return $self;
    }
}
