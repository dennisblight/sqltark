<?php

declare(strict_types=1);

namespace SqlTark\Query\Traits;

use SqlTark\Component\ComponentType;
use SqlTark\Component\LimitClause;
use SqlTark\Component\OffsetClause;
use SqlTark\Query\BaseQuery;
use SqlTark\Query\Interfaces\QueryInterface;

trait PagingTrait
{
    /**
     * @return $this Self object
     */
    public function limit(int $limit): QueryInterface
    {
        $component = new LimitClause;
        $component->setLimit($limit);

        /** @var BaseQuery $this */
        return $this->addOrReplaceComponent(ComponentType::Limit, $component);
    }

    /**
     * @return $this Self object
     */
    public function offset(int $offset): QueryInterface
    {
        $component = new OffsetClause;
        $component->setOffset($offset);

        /** @var BaseQuery */
        return $this->addOrReplaceComponent(ComponentType::Offset, $component);
    }

    /**
     * @return $this Self object
     */
    public function take(int $take): QueryInterface
    {
        return $this->limit($take);
    }

    /**
     * @return $this Self object
     */
    public function skip(int $skip): QueryInterface
    {
        return $this->offset($skip);
    }

    /**
     * @return $this Self object
     */
    public function forPage(int $page, int $perPage = 20): QueryInterface
    {
        return $this->skip(($page - 1) * $perPage)->take($perPage);
    }
}