<?php

declare(strict_types=1);

namespace SqlTark\Query\Traits;

use InvalidArgumentException;
use SqlTark\Component\AggregateClause;
use SqlTark\Component\CombineClause;
use SqlTark\Component\CombineType;
use SqlTark\Component\ComponentType;
use SqlTark\Component\FromClause;
use SqlTark\Helper;
use SqlTark\Query\MethodType;
use SqlTark\Query\Query;

trait CteTrait
{
    /**
     * @param callable|Query $query
     * @param ?string $alias
     * @return $this Self object
     */
    public function with($query, ?string $alias = null)
    {
        $query = Helper::resolveQuery($query, $this);

        $alias = $alias ?? $query->getAlias();
        if(empty($alias)) {
            throw new InvalidArgumentException(
                "No alias found for CTE query"
            );
        }

        $component = new FromClause;
        $component->setTable($query);
        $component->setAlias($alias);

        return $this->addComponent(ComponentType::CTE, $component);
    }
}