<?php
namespace SqlTark\Clauses\Combine;

use SqlTark\Clauses\AbstractClause;

class RawCombine extends AbstractCombine
{
    /**
     * @var string $expression
     */
    protected $expression;
    
    /**
     * @var array $bindings
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

    public function getBindings(): array
    {
        return $this->bindings;
    }

    public function setBindings(array $value)
    {
        $this->bindings = $value;
    }

    /**
     * @return RawCombine
     */
    public function clone(): AbstractClause
    {
        /** @var RawCombine */
        $self = parent::clone();
        $self->expression = $this->expression;
        $self->bindings = $this->bindings;
        return $self;
    }
}