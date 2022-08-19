<?php

declare(strict_types=1);

namespace SqlTark\Query;

use InvalidArgumentException;
use SqlTark\Component\AdHocTableFromClause;
use SqlTark\Component\ComponentType;
use SqlTark\Component\QueryFromClause;
use SqlTark\Query\Interfaces\ConditionInterface;
use SqlTark\Query\Traits\ConditionTrait;

/**
 * @method Query from(Query $query, ?string $alias = null)
 * 
 * @method Query from(callable $callback, ?string $alias = null)
 */
class Query extends BaseQuery implements ConditionInterface
{
    use ConditionTrait;

    public function __construct($table = null)
    {
        $this->from($table);
    }

    /**
     * @return Query
     */
    public function from($table, ?string $alias = null): BaseQuery
    {
        $component = null;
        if (is_callable($table)) {
            $query = $this->newQuery()->setParent($this);
            return $this->from($table($query), $alias);
        } elseif ($table instanceof Query) {
            $component = new QueryFromClause;

            $component->setQuery($table);
            $component->setAlias($alias);

            return $this->addComponent(ComponentType::From, $component);
        } else {
            return parent::from($table, $alias);
        }
    }

    public function fromAdHoc(string $alias, array $columns, ?array $values = null): Query
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

        return $this->addComponent(ComponentType::From, $component);
    }
}
