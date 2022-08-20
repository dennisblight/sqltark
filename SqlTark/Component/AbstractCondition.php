<?php

declare(strict_types=1);

namespace SqlTark\Component;

abstract class AbstractCondition extends AbstractComponent
{
    /**
     * @var bool $isOr
     */
    protected $isOr = false;

    /**
     * @var bool $isNot
     */
    protected $isNot = false;

    public function getOr(): bool
    {
        return $this->isOr;
    }

    public function setOr(bool $value)
    {
        $this->isOr = $value;
    }

    public function getNot(): bool
    {
        return $this->isNot;
    }

    public function setNot(bool $value)
    {
        $this->isNot = $value;
    }

    /**
     * @return AbstractCondition
     */
    public function clone(): AbstractComponent
    {
        /** @var AbstractCondition */
        $self = parent::clone();
        $self->isOr = $this->isOr;
        $self->isNot = $this->isNot;
        return $self;
    }
}