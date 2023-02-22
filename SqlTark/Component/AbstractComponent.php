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

    final public function __construct() { }

    /**
     * @return $this Self object
     */
    public function setComponentType(int $value)
    {
        $this->componentType = $value;
        return $this;
    }
}
