<?php

declare(strict_types=1);

namespace SqlTark\Query\Traits;

use SqlTark\Component\AggregateClause;
use SqlTark\Component\ComponentType;
use SqlTark\Helper;
use SqlTark\Query\Interfaces\QueryInterface;
use SqlTark\Query\MethodType;

trait AggregateTrait
{
    /**
     * @param string $type Aggregate type
     * @return static Self object
     */
    public function asAggregate(string $type, $column = null): QueryInterface
    {
        $column = Helper::resolveQuery($column, $this);
        $column = Helper::resolveExpression($column, 'column');

        $this->setMethod(MethodType::Aggregate);

        $component = new AggregateClause;
        $component->setType($type);
        $component->setColumn($column);

        return $this->addOrReplaceComponent(ComponentType::Aggregate, $component);
    }

    /**
     * @return static
     */
    public function asCount($column = null): QueryInterface
    {
        return $this->asAggregate('COUNT', $column);
    }

    /**
     * @return static
     */
    public function asAvg($column = null): QueryInterface
    {
        return $this->asAggregate('AVG', $column);
    }

    /**
     * @return static
     */
    public function asAverage($column = null): QueryInterface
    {
        return $this->asAggregate('AVG', $column);
    }

    /**
     * @return static
     */
    public function asSum($column = null): QueryInterface
    {
        return $this->asAggregate('SUM', $column);
    }

    /**
     * @return static
     */
    public function asMax($column = null): QueryInterface
    {
        return $this->asAggregate('MAX', $column);
    }

    /**
     * @return static
     */
    public function asMin($column = null): QueryInterface
    {
        return $this->asAggregate('MIN', $column);
    }
}