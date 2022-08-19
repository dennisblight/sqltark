<?php

declare(strict_types=1);

namespace SqlTark\Component;

abstract class AbstractFrom extends AbstractComponent
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
    public function clone(): AbstractComponent
    {
        /** @var AbstractFrom */
        $self = parent::clone();

        $self->alias = $this->alias;

        return $self;
    }
}
