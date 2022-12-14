<?php

declare(strict_types=1);

namespace SqlTark\Query;

use SqlTark\Query\Interfaces\ConditionInterface;
use SqlTark\Query\Traits\BasicFromTrait;
use SqlTark\Query\Traits\ConditionTrait;
use SqlTark\Query\Traits\JoinTrait;
use SqlTark\Query\Traits\OrderTrait;
use SqlTark\Query\Traits\PagingTrait;

/**
 * @deprecated Use Query.asDelete instead
 */
class DeleteQuery extends BaseQuery implements ConditionInterface
{
    use BasicFromTrait, JoinTrait, ConditionTrait, PagingTrait, OrderTrait;

    /**
     * @var int $method
     */
    protected $method = MethodType::Delete;

    public function __construct($table = null)
    {
        if (!is_null($table)) {
            $this->from($table);
        }
    }

    /**
     * Create delete query from select query
     * @return static
     */
    public static function fromQuery(Query $query)
    {
        $self = new self;
        $self->components = $query->components;
        return $self;
    }
}