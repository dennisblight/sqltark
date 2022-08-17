<?php
namespace SqlTark\Clauses\From;

use SqlTark\Clauses\AbstractClause;

class RawFromClause extends AbstractFrom
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
     * @return RawFromClause
     */
    public function clone(): AbstractClause
    {
        /** @var RawFromClause */
        $self = parent::clone();

        $self->expression = $this->expression;
        $self->bindings = $this->bindings;

        return $self;
    }
}