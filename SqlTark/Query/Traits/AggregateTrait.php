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
     * @return $this Self object
     */
    public function asAggregate(string $type, $column = null)
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
     * @return $this Self object
     */
    public function asCount($column = null)
    {
        return $this->asAggregate('COUNT', $column);
    }

    /**
     * @return $this Self object
     */
    public function asAvg($column = null)
    {
        return $this->asAggregate('AVG', $column);
    }

    /**
     * @return $this Self object
     */
    public function asAverage($column = null)
    {
        return $this->asAggregate('AVG', $column);
    }

    /**
     * @return $this Self object
     */
    public function asSum($column = null)
    {
        return $this->asAggregate('SUM', $column);
    }

    /**
     * @return $this Self object
     */
    public function asMax($column = null)
    {
        return $this->asAggregate('MAX', $column);
    }

    /**
     * @return $this Self object
     */
    public function asMin($column = null)
    {
        return $this->asAggregate('MIN', $column);
    }
}