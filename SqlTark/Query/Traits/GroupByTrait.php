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
use SqlTark\Helper;
use SqlTark\Query\Interfaces\QueryInterface;
use SqlTark\Query\Query;

trait GroupByTrait
{
    /**
     * @return static Self object
     */
    public function groupBy(...$columns): QueryInterface
    {
        if (func_num_args() == 1 && is_iterable($columns[0])) {
            $columns = $columns[0];
        }

        foreach ($columns as $column) {
            $column = Helper::resolveQuery($column, $this);
            $column = Helper::resolveExpression($column, 'column');

            $component = new ColumnClause;
            $component->setColumn($column);

            /** @var BaseQuery $this */
            $this->addComponent(ComponentType::GroupBy, $component);
        }

        return $this;
    }

    /**
     * @return static Self object
     */
    public function groupByRaw(string $expression, ...$bindings): QueryInterface
    {
        $resolvedBindings = new SplFixedArray(count($bindings));
        foreach ($bindings as $index => $item) {
            if (is_scalar($item) || is_null($item) || $item instanceof \DateTime) {
                $resolvedBindings[$index] = Expressions::literal($item);
            } elseif ($item instanceof BaseExpression) {
                $resolvedBindings[$index] = $item;
            } else {
                $class = Helper::getType($item);
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