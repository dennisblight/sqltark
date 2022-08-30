<?php

declare(strict_types=1);

namespace SqlTark\Query\Traits;

use SqlTark\Component\AggregateClause;
use SqlTark\Component\CombineClause;
use SqlTark\Component\CombineType;
use SqlTark\Component\ComponentType;
use SqlTark\Helper;
use SqlTark\Query\MethodType;
use SqlTark\Query\Query;

trait CombineTrait
{
    /**
     * @param callable|Query $query
     * @param int $operation
     * @param bool $all
     * @return $this Self object
     */
    public function combine($query, int $operation = CombineType::Union, bool $all = false)
    {
        $query = Helper::resolveQuery($query, $this);

        $component = new CombineClause;
        $component->setQuery($query);
        $component->setOperation($operation);
        $component->setAll($all);

        return $this->addComponent(ComponentType::Combine, $component);
    }

    public function union($query)
    {
        return $this->combine($query, CombineType::Union, false);
    }

    public function unionAll($query)
    {
        return $this->combine($query, CombineType::Union, true);
    }

    public function except($query)
    {
        return $this->combine($query, CombineType::Except, false);
    }

    public function exceptAll($query)
    {
        return $this->combine($query, CombineType::Except, true);
    }

    public function intersect($query)
    {
        return $this->combine($query, CombineType::Intersect, false);
    }

    public function intersectAll($query)
    {
        return $this->combine($query, CombineType::Intersect, true);
    }
}