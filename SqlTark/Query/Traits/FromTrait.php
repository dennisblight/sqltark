<?php

declare(strict_types=1);

namespace SqlTark\Query\Traits;

use SplFixedArray;
use SqlTark\Expressions;
use SqlTark\Query\Query;
use InvalidArgumentException;
use SqlTark\Component\FromClause;
use SqlTark\Component\ComponentType;
use SqlTark\Component\RawFromClause;
use SqlTark\Expressions\BaseExpression;
use SqlTark\Component\AdHocTableFromClause;
use SqlTark\Helper;
use SqlTark\Query\Interfaces\QueryInterface;

/**
 * @method static from(string $table, ?string $alias = null)
 * 
 * @method static from(Query $query, ?string $alias = null)
 * 
 * @method static from(callable $callback, ?string $alias = null)
 */
trait FromTrait
{
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
     * @return $this Self object
     */
    public function alias(?string $alias): QueryInterface
    {
        $this->alias = $alias;
        return $this;
    }

    public function from($table, ?string $alias = null): QueryInterface
    {
        $table = Helper::resolveQuery($table, $this);

        if (!is_string($table) && !($table instanceof Query)) {
            $class = Helper::getType($table);
            throw new InvalidArgumentException("Could not resolve '$class' as table");
        }

        if ($table instanceof Query) {
            if (empty($alias ?: $table->getAlias())) {
                throw new InvalidArgumentException(
                    "No Alias found for sub query from"
                );
            }
        }

        $component = new FromClause;
        $component->setTable($table);
        $component->setAlias($alias);

        return $this->addOrReplaceComponent(ComponentType::From, $component);
    }

    /**
     * @return $this Self object
     */
    public function fromAdHoc(string $alias, iterable $columns, ?iterable $values = null): QueryInterface
    {
        $columnCount = null;
        $resolvedColumns = null;
        if(func_num_args() == 2)
        {
            $resolvedColumns = [];
            $columnCount = 0;
            foreach($columns as $row)
            {
                foreach($row as $column => $value)
                {
                    $resolvedColumns[] = $column;
                    $columnCount++;
                }
                break;
            }

            $resolvedColumns = SplFixedArray::fromArray($resolvedColumns);
            $values = $columns;
            $columns = $resolvedColumns;
        }

        $columnCount = $columnCount ?? Helper::countIterable($columns);
        if($columnCount == 0)
        {
            throw new InvalidArgumentException(
                "Could not create ad hoc table with no columns"
            );
        }

        if(is_null($resolvedColumns))
        {
            $resolvedColumns = new SplFixedArray($columnCount);

            $index = 0;
            foreach($columns as $column)
            {
                if(!is_scalar($column))
                {
                    $class = Helper::getType($column);
                    throw new InvalidArgumentException(
                        "Columns must be string. '$class' found"
                    );
                }

                $resolvedColumns[$index] = (string) $column;
                $index++;
            }
        }

        $rowsCount = Helper::countIterable($values);
        if($rowsCount == 0)
        {
            throw new InvalidArgumentException(
                "Could not create ad hoc table with no rows"
            );
        }

        $resolvedRows = new SplFixedArray($rowsCount);
        $rowIndex = 0;
        foreach ($values as $row)
        {
            $resolvedRow = new SplFixedArray($columnCount);
            $columnIndex = 0;
            foreach($row as $value)
            {
                $resolvedRow[$columnIndex] = Helper::resolveLiteral($value, 'value');
                $columnIndex++;
            }

            if ($columnIndex != $columnCount) {
                throw new InvalidArgumentException(
                    "Array values count must same with columns count."
                );
            }

            $resolvedRows[$rowIndex] = $resolvedRow;
            $rowIndex++;
        }

        $component = new AdHocTableFromClause;
        $component->setAlias($alias);
        $component->setColumns($resolvedColumns);
        $component->setValues($resolvedRows);

        return $this->addOrReplaceComponent(ComponentType::From, $component);
    }

    /**
     * @return $this Self object
     */
    public function fromRaw(string $expression, ...$bindings): QueryInterface
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

        $component = new RawFromClause;

        $component->setExpression($expression);
        $component->setBindings($resolvedBindings);

        return $this->addOrReplaceComponent(ComponentType::From, $component);
    }
}
