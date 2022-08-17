<?php

declare(strict_types=1);

namespace SqlTark\Clauses\OrderBy;

use SqlTark\Clauses\AbstractClause;

class RawOrderBy extends AbstractOrderBy
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
     * @return RawOrderBy
     */
    public function clone(): AbstractClause
    {
        /** @var RawOrderBy */
        $self = parent::clone();

        $self->expression = $this->expression;
        $self->bindings = $this->bindings;

        return $self;
    }
}
