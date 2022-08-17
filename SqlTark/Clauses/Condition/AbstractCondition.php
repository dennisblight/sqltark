<?php
namespace SqlTark\Clauses\Condition;

use SqlTark\Clauses\AbstractClause;

abstract class AbstractCondition extends AbstractClause
{
    /**
     * @var bool $isOr
     */
    protected $isOr = false;
    
    /**
     * @var bool $isNot
     */
    protected $isNot = false;

    public function getIsOr(): bool
    {
        return $this->isOr;
    }

    public function setIsOr(bool $value)
    {
        $this->isOr = $value;
    }

    public function getIsNot(): bool
    {
        return $this->isNot;
    }

    public function setIsNot(bool $value)
    {
        $this->isNot = $value;
    }

    /**
     * @return AbstractCondition
     */
    public function clone(): AbstractClause
    {
        /** @var AbstractCondition */
        $self = parent::clone();
        $self->isOr = $this->isOr;
        $self->isNot = $this->isNot;
        return $self;
    }
}