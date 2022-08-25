<?php

declare(strict_types=1);

namespace SqlTark\Query\Traits;

use SqlTark\Component\ComponentType;
use SqlTark\Component\LimitClause;
use SqlTark\Component\OffsetClause;

trait PagingTrait
{
    /**
     * @return $this Self object
     */
    public function limit(int $limit)
    {
        $component = new LimitClause;
        $component->setLimit($limit);

        return $this->addOrReplaceComponent(ComponentType::Limit, $component);
    }

    /**
     * @return $this Self object
     */
    public function offset(int $offset)
    {
        $component = new OffsetClause;
        $component->setOffset($offset);

        return $this->addOrReplaceComponent(ComponentType::Offset, $component);
    }

    /**
     * @return $this Self object
     */
    public function take(int $take)
    {
        return $this->limit($take);
    }

    /**
     * @return $this Self object
     */
    public function skip(int $skip)
    {
        return $this->offset($skip);
    }

    /**
     * @return $this Self object
     */
    public function forPage(int $page, int $perPage = 20)
    {
        return $this->skip(($page - 1) * $perPage)->take($perPage);
    }
}