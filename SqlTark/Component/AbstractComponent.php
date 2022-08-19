<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Compiler\EngineType;
use SqlTark\Component\ComponentType;

abstract class AbstractComponent
{
    /**
     * @var int $componentType
     */
    protected $componentType;

    /**
     * @var int $engine
     */
    protected $engine;

    public function getComponentType(): int
    {
        return $this->componentType;
    }

    public function getComponentName(): ?string
    {
        return ComponentType::nameOf($this->componentType);
    }

    public function setComponentType(int $value): AbstractComponent
    {
        $this->componentType = $value;
        return $this;
    }

    public function getEngine(): int
    {
        return $this->engine;
    }

    public function getEngineName(): ?string
    {
        return EngineType::nameOf($this->engine);
    }

    public function setEngine(int $value): AbstractComponent
    {
        $this->engine = $value;
        return $this;
    }

    public function __clone()
    {
        return $this->clone();
    }

    public function clone()
    {
        $self = new static();

        $self->componentType = $this->componentType;
        $self->engine = $this->engine;

        return $self;
    }
}
