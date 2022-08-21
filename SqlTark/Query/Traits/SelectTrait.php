<?php

declare(strict_types=1);

namespace SqlTark\Query\Traits;

use SplFixedArray;
use InvalidArgumentException;
use SqlTark\Component\ColumnClause;
use SqlTark\Component\ComponentType;
use SqlTark\Component\RawColumn;
use SqlTark\Expressions;
use SqlTark\Expressions\BaseExpression;
use SqlTark\Query\BaseQuery;
use SqlTark\Query\Interfaces\QueryInterface;
use SqlTark\Query\Query;

trait SelectTrait
{
    /**
     * @return static
     */
    public function select(...$columns): QueryInterface
    {
        if (func_num_args() == 1 && is_iterable($columns[0])) {
            return $this->select(...iterator_to_array($columns[0]));
        }

        $hasCallable = false;
        foreach ($columns as $index => $column) {
            if (is_callable($column)) {
                $query = $this->newChild();
                $columns[$index] = $column($query);
                $hasCallable = true;
            }
        }

        if ($hasCallable) {
            return $this->select(...$columns);
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

            $component = new ColumnClause;
            $component->setColumn($column);

            $this->addComponent(ComponentType::Select, $component);
        }

        return $this;
    }

    /**
     * @return static
     */
    public function selectRaw(string $expression, ...$bindings): QueryInterface
    {
        $resolvedBindings = new SplFixedArray(count($bindings));
        foreach ($bindings as $index => $item) {
            if (is_scalar($item) || is_null($bindings)) {
                $resolvedBindings[$index] = Expressions::literal($item);
            } elseif ($item instanceof BaseExpression) {
                $resolvedBindings[$index] = $item;
            } elseif (is_object($item) && method_exists($item, '__toString')) {
                $resolvedBindings[$index] = Expressions::literal((string) $item);
            } else {
                $class = get_class($item);
                throw new InvalidArgumentException(
                    "Could not resolve '$class' as binding."
                );
            }
        }

        $component = new RawColumn;

        $component->setExpression($expression);
        $component->setBindings($resolvedBindings);

        return $this->addComponent(ComponentType::Select, $component);
    }
}
