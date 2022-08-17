<?php
namespace SqlTark\Clauses\From;

use SqlTark\Clauses\AbstractClause;

abstract class AbstractFrom extends AbstractClause
{
    /**
     * @var string $alias
     */
    protected $alias;

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setAlias(string $value)
    {
        $this->alias = $value;
    }

    /**
     * @return AbstractFrom
     */
    public function clone(): AbstractClause
    {
        /** @var AbstractFrom */
        $self = parent::clone();

        $self->alias = $this->alias;

        return $self;
    }
}