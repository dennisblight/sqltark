<?php
declare(strict_types=1);

namespace SqlTark\Query\Traits;

use SplFixedArray;
use SqlTark\Expressions;
use SqlTark\Query\Query;
use InvalidArgumentException;
use SqlTark\Component\ComponentType;
use SqlTark\Component\RawFromClause;
use SqlTark\Component\QueryFromClause;
use SqlTark\Expressions\BaseExpression;
use SqlTark\Component\AdHocTableFromClause;
use SqlTark\Query\Interfaces\QueryInterface;

/**
 * @method static from(Query $query, ?string $alias = null)
 * 
 * @method static from(callable $callback, ?string $alias = null)
 */
trait AdvancedFromTrait
{
    public function from($table, ?string $alias = null): QueryInterface
    {
        $component = null;
        if (is_callable($table)) {
            $query = $this->newQuery()->setParent($this);
            return $this->from($table($query), $alias);
        } elseif ($table instanceof Query) {
            $alias = $alias ?: $table->getAlias();
            if (empty($alias)) {
                throw new InvalidArgumentException(
                    "No Alias found for sub query from"
                );
            }
            $component = new QueryFromClause;

            $component->setQuery($table);
            $component->setAlias($alias);

            return $this->addComponent(ComponentType::From, $component);
        } else {
            return parent::from($table, $alias);
        }
    }

    /**
     * @return static Self object
     */
    public function fromAdHoc(string $alias, array $columns, ?array $values = null): QueryInterface
    {
        $component = new AdHocTableFromClause;

        if (is_null($values)) {
            if (isset($columns[0]) && is_array($columns[0])) {
                $values = $columns;
                $columns = array_keys($columns[0]);
            } else {
                throw new InvalidArgumentException(
                    "Could not resolve 'columns' parameter. Columns should be array of array."
                );
            }
        }

        $columnCount = count($columns);
        foreach ($values as $item) {
            if (is_countable($item)) {
                $count = count($item);
            } elseif (is_object($item) && method_exists($item, 'count')) {
                $count = $item->count();
            } else {
                $class = get_class($item);
                throw new InvalidArgumentException(
                    "Array values '$class' must countable."
                );
            }

            if ($count != $columnCount) {
                throw new InvalidArgumentException(
                    "Array values count must same with columns count."
                );
            }
        }

        $component->setAlias($alias);
        $component->setColumns($columns);
        $component->setValues(array_values($values));

        return $this->addOrReplaceComponent(ComponentType::From, $component);
    }

    /**
     * @return static Self object
     */
    public function fromRaw(string $expression, ...$bindings): QueryInterface
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

        $component = new RawFromClause;

        $component->setExpression($expression);
        $component->setBindings($resolvedBindings);

        return $this->addOrReplaceComponent(ComponentType::From, $component);
    }
}