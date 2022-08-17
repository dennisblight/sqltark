<?php

declare(strict_types=1);

namespace SqlTark\Clauses;

abstract class AbstractClause
{
    /**
     * @var string $engine
     */
    protected $engine;

    /**
     * @var string $component
     */
    protected $component;

    public function getEngine(): string
    {
        return $this->engine;
    }

    public function setEngine(string $value): AbstractClause
    {
        $this->engine = $value;
        return $this;
    }

    public function getComponent(): string
    {
        return $this->component;
    }

    public function setComponent(string $value): AbstractClause
    {
        $this->component = $value;
        return $this;
    }

    public function clone(): AbstractClause
    {
        $self = new static();
        $self->engine = $this->engine;
        $self->component = $this->component;

        return $self;
    }

    public function __clone()
    {
        return $this->clone();
    }
}
