<?php

declare(strict_types=1);

namespace SqlTark\Query;

use SqlTark\Query\BaseQuery;
use SqlTark\Query\Traits\CteTrait;
use SqlTark\Query\Traits\FromTrait;
use SqlTark\Query\Traits\JoinTrait;
use SqlTark\Query\Traits\OrderTrait;
use SqlTark\Query\Traits\HavingTrait;
use SqlTark\Query\Traits\PagingTrait;
use SqlTark\Query\Traits\SelectTrait;
use SqlTark\Query\Traits\CombineTrait;
use SqlTark\Query\Traits\GroupByTrait;
use SqlTark\Query\Traits\AggregateTrait;
use SqlTark\Query\Traits\ConditionTrait;
use SqlTark\Query\Interfaces\HavingInterface;
use SqlTark\Query\Interfaces\ConditionInterface;

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
        HavingTrait,
        CombineTrait,
        CteTrait;

    public function __construct($table = null)
    {
        if (!is_null($table)) {
            $this->from($table);
        }
    }

    /**
     * @return static Clone of current object
     */
    public function clone()
    {
        $self = parent::clone();

        $self->alias = $this->alias;
        $self->distinct = $this->distinct;
        $self->orFlag = $this->orFlag;
        $self->notFlag = $this->notFlag;
        $self->havingFlag = $this->havingFlag;

        return $self;
    }

    /**
     * @return InsertQuery
     */
    public function insert(iterable $columns, ?iterable $values = null)
    {
        $result = InsertQuery::fromQuery($this);
        return call_user_func_array([$result, 'withValues'], func_get_args());
    }

    /**
     * @return InsertQuery
     */
    public function insertQuery(Query $query, ?iterable $columns = null)
    {
        $result = InsertQuery::fromQuery($this);
        return call_user_func_array([$result, 'withQuery'], func_get_args());
    }

    /**
     * @param iterable|object $value
     * @return UpdateQuery
     */
    public function update($value)
    {
        $result = UpdateQuery::fromQuery($this);
        return call_user_func_array([$result, 'withValue'], func_get_args());
    }

    /**
     * @return DeleteQuery
     */
    public function delete()
    {
        return DeleteQuery::fromQuery($this);
    }
}
