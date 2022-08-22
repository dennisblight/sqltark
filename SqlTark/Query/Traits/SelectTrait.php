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
use SqlTark\Helper;
use SqlTark\Query\Interfaces\QueryInterface;

trait SelectTrait
{
    /**
     * @var bool $distinct
     */
    protected $distinct = false;

    public function isDistict(): bool
    {
        return $this->distinct;
    }

    /**
     * @return static
     */
    public function distinct($value = true): QueryInterface
    {
        $this->distinct = $value;
        return $this;
    }

    /**
     * @return static
     */
    public function select(...$columns): QueryInterface
    {
        if (func_num_args() == 1 && is_iterable($columns[0])) {
            $columns = $columns[0];
        }

        foreach ($columns as $column) {
            $column = Helper::resolveQuery($column, $this);
            $column = Helper::resolveExpression($column, 'column');

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

        return $this->addComponent(ComponentType::Select, $component);
    }
}
