<?php

declare(strict_types=1);

namespace SqlTark\Query;

use InvalidArgumentException;
use SqlTark\Component\ComponentType;
use SqlTark\Component\UpdateClause;
use SqlTark\Helper;
use SqlTark\Query\Interfaces\ConditionInterface;
use SqlTark\Query\Traits\BasicFromTrait;
use SqlTark\Query\Traits\ConditionTrait;
use SqlTark\Query\Traits\OrderTrait;
use SqlTark\Query\Traits\PagingTrait;

class UpdateQuery extends BaseQuery implements ConditionInterface
{
    use BasicFromTrait, ConditionTrait, PagingTrait, OrderTrait;

    /**
     * @var int $method
     */
    protected $method = MethodType::Update;

    public function __construct($table = null)
    {
        if (!is_null($table)) {
            $this->from($table);
        }
    }

    /**
     * Create update query from select query
     * @return $this Self object
     */
    public static function fromQuery(Query $query)
    {
        $self = new self;
        $self->components = $query->components;
        return $self;
    }

    /**
     * @param iterable|object $value
     * @return $this Self object
     */
    public function withValue($value)
    {
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