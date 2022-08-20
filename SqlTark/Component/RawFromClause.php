<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SplFixedArray;
use SqlTark\Expressions\BaseExpression;

class RawFromClause extends AbstractFrom
{
    /**
     * @var string $expression
     */
    protected $expression;

    /**
     * @var SplFixedArray $bindings
     */
    protected $bindings;

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function setExpression(string $value)
    {
        $this->expression = $value;
    }

    /**
     * @return SplFixedArray<BaseExpression>
     */
    public function getBindings(): SplFixedArray
    {
        return $this->bindings;
    }

    /**
     * @param SplFixedArray<BaseExpression> $value
     */
    public function setBindings(SplFixedArray $value)
    {
        $this->bindings = $value;
    }

    /**
     * @return RawFromClause
     */
    public function clone(): AbstractComponent
    {
        /** @var RawFromClause */
        $self = parent::clone();

        $self->expression = $this->expression;
        $self->bindings = $this->bindings;

        return $self;
    }
}