<?php

declare(strict_types=1);

namespace SqlTark\Query\Traits;

use InvalidArgumentException;
use SplFixedArray;
use SqlTark\Component\ColumnClause;
use SqlTark\Component\ComponentType;
use SqlTark\Component\RawColumn;
use SqlTark\Expressions;
use SqlTark\Expressions\BaseExpression;
use SqlTark\Query\Interfaces\QueryInterface;
use SqlTark\Query\Query;

trait GroupByTrait
{
    /**
     * @return static Self object
     */
    public function groupBy($columns): QueryInterface
    {
        if(is_callable($columns))
        {
            $query = $this->newChild();
            return $this->groupBy($columns($query));
        }
        elseif(is_iterable($columns))
        {
            foreach($columns as $column)
            {
                if(is_iterable($column))
                {
                    throw new InvalidArgumentException(
                        "Could not resolve iterable inside iterable for columns."
                    );
                }

                $this->groupBy($column);
            }

            return $this;
        }

        $resolvedColumn = null;
        if(is_string($columns))
        {
            $resolvedColumn = Expressions::column($columns);
        }
        elseif(is_scalar($columns) || is_null($columns))
        {
            $resolvedColumn = Expressions::literal($columns);
        }
        elseif(is_object($columns) && method_exists($columns, '__toString'))
        {
            $resolvedColumn = Expressions::literal((string) $columns);
        }
        elseif($columns instanceof Query || $columns instanceof BaseExpression)
        {
            $resolvedColumn = $columns;
        }
        else
        {
            $class = get_class($columns);
            throw new InvalidArgumentException(
                "Could not resolve '$class' from columns"
            );
        }

        $component = new ColumnClause;
        $component->setColumn($resolvedColumn);

        $this->addComponent(ComponentType::GroupBy, $component);
    }

    /**
     * @return static Self object
     */
    public function groupByRaw(string $expression, ...$bindings): QueryInterface
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

        return $this->addComponent(ComponentType::GroupBy, $component);
    }
}