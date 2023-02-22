<?php

declare(strict_types=1);

namespace SqlTark\Query;

use SplFixedArray;
use SqlTark\Helper;
use SqlTark\Query\BaseQuery;
use InvalidArgumentException;
use SqlTark\Query\MethodType;
use SqlTark\Query\Traits\CteTrait;
use SqlTark\Component\UpdateClause;
use SqlTark\Component\InsertClause;
use SqlTark\Query\Traits\FromTrait;
use SqlTark\Query\Traits\JoinTrait;
use SqlTark\Component\ComponentType;
use SqlTark\Query\Traits\OrderTrait;
use SqlTark\Query\Traits\HavingTrait;
use SqlTark\Query\Traits\PagingTrait;
use SqlTark\Query\Traits\SelectTrait;
use SqlTark\Query\Traits\CombineTrait;
use SqlTark\Query\Traits\GroupByTrait;
use SqlTark\Component\InsertQueryClause;
use SqlTark\Query\Traits\AggregateTrait;
use SqlTark\Query\Traits\ConditionTrait;
use SqlTark\Query\Interfaces\HavingInterface;
use SqlTark\Query\Interfaces\ConditionInterface;

class Query extends BaseQuery implements ConditionInterface, HavingInterface
{
    use FromTrait,
        SelectTrait,
        AggregateTrait,
        JoinTrait,
        ConditionTrait,
        OrderTrait,
        PagingTrait,
        GroupByTrait,
        HavingTrait,
        CombineTrait,
        CteTrait;

    public function __construct($table = null)
    {
        if (!is_null($table)) {
            $this->from($table);
        }
    }

    /**
     * @return $this Self object
     */
    public function asDelete()
    {
        $this->method = MethodType::Delete;
        return $this;
    }

    /**
     * @return $this Self object
     */
    public function asInsert(iterable $columns, ?iterable $values = null)
    {
        $this->method = MethodType::Insert;
        
        $columnCount = null;
        $resolvedColumns = null;
        if(func_num_args() == 1)
        {
            $resolvedColumns = [];
            $resolvedValues = [];
            $columnCount = 0;
            $expectIterable = true;
            foreach($columns as $col => $row)
            {
                if(is_iterable($row)) {
                    if(!$expectIterable) {
                        throw new InvalidArgumentException(
                            "Could not resolve iterable as value"
                        );
                    }
                    foreach($row as $column => $value)
                    {
                        $resolvedColumns[] = $column;
                        $columnCount++;
                    }
                    break;
                }
                else {
                    $expectIterable = false;
                    $resolvedColumns[] = $col;
                    $resolvedValues[] = $row;
                    $columnCount++;
                }
            }

            $resolvedColumns = SplFixedArray::fromArray($resolvedColumns);
            if($expectIterable) {
                $values = $columns;
            }
            else {
                $values = [$resolvedValues];
            }
            
            $columns = $resolvedColumns;
        }

        $columnCount = $columnCount ?? Helper::countIterable($columns);
        if($columnCount == 0)
        {
            throw new InvalidArgumentException(
                "Could not set values with no columns"
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
                "Could not set values with no rows"
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

        $component = new InsertClause;
        $component->setColumns($resolvedColumns);
        $component->setValues($resolvedRows);

        return $this->addOrReplaceComponent(ComponentType::Insert, $component);
    }

    /**
     * @return $this Self object
     */
    public function asInsertWithQuery(Query $query, ?iterable $columns = null)
    {
        $this->method = MethodType::Insert;
        
        $component = new InsertQueryClause;
        $component->setQuery($query);
        $component->setColumns($columns);

        return $this->addOrReplaceComponent(ComponentType::Insert, $component);
    }

    /**
     * @return $this Self object
     */
    public function asUpdate($value)
    {
        $this->method = MethodType::Update;

        if(is_scalar($value) || is_null($value)) {
            throw new InvalidArgumentException(
                "Update value must be object or iterable"
            );
        }

        $resolvedValue = [];
        foreach($value as $column => $item) {
            if(!is_string($column)) {
                $class = Helper::getType($column);
                throw new InvalidArgumentException(
                    "Column must be string. '$class' found"
                );
            }

            $resolvedValue[$column] = Helper::resolveLiteral($item, 'item');
        }

        if(empty($resolvedValue)) {
            $class = Helper::getType($value);
            throw new InvalidArgumentException(
                "Could not resolve '$class' for updated value"
            );
        }

        $component = new UpdateClause;
        $component->setValue($resolvedValue);

        return $this->addOrReplaceComponent(ComponentType::Update, $component);
    }
}
