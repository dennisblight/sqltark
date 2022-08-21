<?php

declare(strict_types=1);

namespace SqlTark\Query;

use SqlTark\Query\BaseQuery;
use SqlTark\Query\Traits\OrderTrait;
use SqlTark\Query\Traits\PagingTrait;
use SqlTark\Query\Traits\SelectTrait;
use SqlTark\Query\Traits\ConditionTrait;
use SqlTark\Query\Traits\AdvancedFromTrait;
use SqlTark\Query\Interfaces\ConditionInterface;
use SqlTark\Query\Traits\AggregateTrait;
use SqlTark\Query\Traits\GroupByTrait;

class Query extends BaseQuery implements ConditionInterface
{
    use SelectTrait,
        ConditionTrait,
        PagingTrait,
        OrderTrait,
        AdvancedFromTrait,
        AggregateTrait,
        GroupByTrait;

    /**
     * @var bool $distinct
     */
    protected $distinct = false;

    /**
     * @var ?string $alias
     */
    protected $alias;

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return static
     */
    public function alias(?string $alias)
    {
        $this->alias = $alias;
        return $this;
    }

    public function isDistict(): bool
    {
        return $this->distinct;
    }

    /**
     * @return static
     */
    public function distinct($value = true): Query
    {
        $this->distinct = $value;
        return $this;
    }

    public function __construct($table = null)
    {
        if (!is_null($table)) {
            $this->from($table);
        }
    }
}
