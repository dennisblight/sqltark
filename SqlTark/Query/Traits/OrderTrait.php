<?php

declare(strict_types=1);

namespace SqlTark\Query\Traits;

use SqlTark\Expressions;
use SqlTark\Query\Query;
use InvalidArgumentException;
use SqlTark\Component\OrderClause;
use SqlTark\Component\RandomOrder;
use SqlTark\Component\ComponentType;
use SqlTark\Expressions\BaseExpression;
use SqlTark\Query\Interfaces\QueryInterface;

trait OrderTrait
{
    /**
     * @return static Self object
     */
    public function orderBy(...$columns): QueryInterface
    {
        if (func_num_args() == 1 && is_iterable($columns[0])) {
            return $this->orderBy(...iterator_to_array($columns[0]));
        }

        $hasCallable = false;
        foreach ($columns as $index => $column) {
            if (is_callable($column)) {
                /** @var BaseQuery $this */
                $query = $this->newChild();
                $columns[$index] = $column($query);
                $hasCallable = true;
            }
        }

        if ($hasCallable) {
            return $this->orderBy(...$columns);
        }

        foreach ($columns as $column) {
            if (is_string($column)) {
                $column = Expressions::column($column);
            } elseif (is_scalar($column) || is_null($column) || $column instanceof \DateTime) {
                $column = Expressions::literal($column);
            } elseif (!($column instanceof BaseExpression || $column instanceof Query)) {
                $class = get_class($column);
                throw new InvalidArgumentException(
                    "Could not resolve '$class' for column parameter."
                );
            }

            $component = new OrderClause;
            $component->setColumn($column);
            $component->setAscending(true);

            /** @var BaseQuery $this */
            $this->addComponent(ComponentType::OrderBy, $component);
        }

        return $this;
    }

    /**
     * @return static Self object
     */
    public function orderByDesc(...$columns): QueryInterface
    {
        if (func_num_args() == 1 && is_iterable($columns[0])) {
            return $this->orderByDesc(...iterator_to_array($columns[0]));
        }

        $hasCallable = false;
        foreach ($columns as $index => $column) {
            if (is_callable($column)) {
                /** @var BaseQuery $this */
                $query = $this->newChild();
                $columns[$index] = $column($query);
                $hasCallable = true;
            }
        }

        if ($hasCallable) {
            return $this->orderByDesc(...$columns);
        }

        foreach ($columns as $column) {
            if (is_string($column)) {
                $column = Expressions::column($column);
            } elseif (is_scalar($column) || is_null($column) || $column instanceof \DateTime) {
                $column = Expressions::literal($column);
            } elseif (!($column instanceof BaseExpression || $column instanceof Query)) {
                $class = get_class($column);
                throw new InvalidArgumentException(
                    "Could not resolve '$class' for column parameter."
                );
            }

            $component = new OrderClause;
            $component->setColumn($column);
            $component->setAscending(false);

            /** @var BaseQuery $this */
            $this->addComponent(ComponentType::OrderBy, $component);
        }

        return $this;
    }

    /**
     * @return static Self object
     */
    public function orderByRandom(): QueryInterface
    {
        /** @var BaseQuery $this */
        return $this->addComponent(ComponentType::OrderBy, new RandomOrder);
    }
}
