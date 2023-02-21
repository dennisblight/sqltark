<?php

declare(strict_types=1);

namespace SqlTark\Query\Traits;

use SqlTark\Component\OrderClause;
use SqlTark\Component\RandomOrder;
use SqlTark\Component\ComponentType;
use SqlTark\Helper;
use SqlTark\Query\BaseQuery;

trait OrderTrait
{
    /**
     * @return $this Self object
     */
    public function orderBy(...$columns)
    {
        if (func_num_args() == 1 && is_iterable($columns[0])) {
            $columns = $columns[0];
        }

        foreach ($columns as $column) {
            $column = Helper::resolveQuery($column, $this);
            $column = Helper::resolveExpression($column, 'column');

            $component = new OrderClause;
            $component->setColumn($column);
            $component->setAscending(true);

            /** @var BaseQuery $this */
            $this->addComponent(ComponentType::OrderBy, $component);
        }

        return $this;
    }

    /**
     * @return $this Self object
     */
    public function orderByDesc(...$columns)
    {
        if (func_num_args() == 1 && is_iterable($columns[0])) {
            $columns = $columns[0];
        }

        foreach ($columns as $column) {
            $column = Helper::resolveQuery($column, $this);
            $column = Helper::resolveExpression($column, 'column');

            $component = new OrderClause;
            $component->setColumn($column);
            $component->setAscending(false);

            /** @var BaseQuery $this */
            $this->addComponent(ComponentType::OrderBy, $component);
        }

        return $this;
    }

    /**
     * @return $this Self object
     */
    public function orderByRandom()
    {
        /** @var BaseQuery $this */
        return $this->addComponent(ComponentType::OrderBy, new RandomOrder);
    }
}
