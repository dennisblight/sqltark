<?php

declare(strict_types=1);

namespace SqlTark\Clauses\Column;

use SqlTark\Clauses\AbstractClause;

abstract class Column extends AbstractColumn
{
    /**
     * @var string $name
     */
    protected $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $value)
    {
        $this->name = $value;
    }

    /**
     * @return Column
     */
    public function clone(): AbstractClause
    {
        /** @var Column */
        $self = parent::clone();

        $self->name = $this->name;

        return $self;
    }
}
