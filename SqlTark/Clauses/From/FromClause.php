<?php

declare(strict_types=1);

namespace SqlTark\Clauses\From;

use SqlTark\Clauses\AbstractClause;

class FromClause extends AbstractFrom
{
    /**
     * @var string $table
     */
    protected $table;

    public function getTable(): string
    {
        return $this->table;
    }

    public function setTable(string $value)
    {
        $this->table = $value;
    }

    public function getAlias(): string
    {
        if (stripos($this->table, ' as ') !== false) {
            $segments = array_filter(explode(' ', $this->table), function ($item) {
                return $item != '';
            });

            return $segments[2];
        }

        return $this->table;
    }

    /**
     * @return FromClause
     */
    public function clone(): AbstractClause
    {
        /** @var FromClause */
        $self = parent::clone();

        $self->table = $this->table;

        return $self;
    }
}
