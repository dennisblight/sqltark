<?php

declare(strict_types=1);

namespace SqlTark\Query;

use InvalidArgumentException;
use SplFixedArray;
use SqlTark\Component\ComponentType;
use SqlTark\Component\InsertClause;
use SqlTark\Component\InsertQueryClause;
use SqlTark\Helper;
use SqlTark\Query\Traits\BasicFromTrait;

class InsertQuery extends BaseQuery
{
    use BasicFromTrait;

    /**
     * @var int $method
     */
    protected $method = MethodType::Insert;

    public function __construct($table = null)
    {
        if (!is_null($table)) {
            $this->from($table);
        }
    }

    /**
     * Create insert query from select query
     * @return InsertQuery
     */
    public static function fromQuery(Query $query)
    {
        $self = new self;
        $self->components = $query->components;
        return $self;
    }

    /**
     * @return $this Self object
     */
    public function withValues(iterable $columns, ?iterable $values = null)
    {
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
            // $values = $expectIterable ? $columns : [array_values($columns)];
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
     * @param Query $query
     * @param iterable<string> $columns
     * @return $this Self object
     */
    public function withQuery(Query $query, ?iterable $columns = null)
    {
        $component = new InsertQueryClause;
        $component->setQuery($query);
        $component->setColumns($columns);

        return $this->addOrReplaceComponent(ComponentType::Insert, $component);
    }
}