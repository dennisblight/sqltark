<?php

declare(strict_types=1);

namespace SqlTark\Query\Traits;

use InvalidArgumentException;
use MethodType;
use SplFixedArray;
use SqlTark\Component\AggregateClause;
use SqlTark\Component\ComponentType;
use SqlTark\Expressions;
use SqlTark\Expressions\BaseExpression;
use SqlTark\Query\Interfaces\QueryInterface;
use SqlTark\Query\Query;

trait AggregateTrait
{
    /**
     * @param string $type Aggregate type
     * @return static Self object
     */
    public function asAggregate(string $type, $column = null): QueryInterface
    {
        if(is_string($column))
        {
            $column = Expressions::column($column);
        }
        elseif(is_scalar($column) || is_null($column))
        {
            $column = Expressions::literal($column);
        }
        elseif(is_callable($column))
        {
            $query = $this->newChild();
            return $this->asAggregate($type, $column($query));
        }
        elseif(is_object($column) && method_exists($column, '__toString'))
        {
            $column = Expressions::column((string) $column);
        }
        elseif(!($column instanceof Query || $column instanceof BaseExpression))
        {
            $class = get_class($column);
            throw new InvalidArgumentException(
                "Could not resolve '$class' for column parameter."
            );
        }

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