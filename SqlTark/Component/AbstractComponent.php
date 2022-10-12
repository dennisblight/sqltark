<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Component\ComponentType;

abstract class AbstractComponent
{
    /**
     * @var int $componentType
     */
    protected $componentType;

    public function getComponentType(): int
    {
        return $this->componentType;
    }

    public function getComponentName(): ?string
    {
        return ComponentType::nameOf($this->componentType);
    }

    /**
     * @return static Clone of current object
     */
    public function setComponentType(int $value)
    {
        $this->componentType = $value;
        return $this;
    }

    public function __clone()
    {
        return $this->clone();
    }

    /**
     * @return static Clone of current object
     */
    public function clone()
    {
        $self = new static();

        $self->componentType = $this->componentType;

        return $self;
    }
}
