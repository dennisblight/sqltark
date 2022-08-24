<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Query\Query;

class UpdateClause extends AbstractFrom
{
    /**
     * @var (BaseExpression|Query)[] $values
     */
    protected $value;

    /**
     * @return (BaseExpression|Query)[]
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param (BaseExpression|Query)[] $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return static Clone of current object
     */
    public function clone()
    {
        $self = parent::clone();

        $self->value = $this->value;

        return $self;
    }
}
