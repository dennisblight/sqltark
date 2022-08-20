<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Expressions\BaseExpression;
use SqlTark\Query\Query;

class BetweenCondition extends AbstractCondition
{
    /**
     * @var BaseExpression|Query $column
     */
    protected $column = null;

    /**
     * @var BaseExpression|Query $lower
     */
    protected $lower = null;

    /**
     * @var BaseExpression|Query $higher
     */
    protected $higher = null;

    /**
     * @return BaseExpression|Query
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @param BaseExpression|Query $value
     */
    public function setColumn($value)
    {
        $this->column = $value;
    }

    /**
     * @return BaseExpression|Query
     */
    public function getLower()
    {
        return $this->lower;
    }

    /**
     * @param BaseExpression|Query $value
     */
    public function setLower($value)
    {
        $this->lower = $value;
    }

    /**
     * @return BaseExpression|Query
     */
    public function getHigher()
    {
        return $this->higher;
    }

    /**
     * @param BaseExpression|Query $value
     */
    public function setHigher($value)
    {
        $this->higher = $value;
    }

    /**
     * @return BetweenCondition
     */
    public function clone(): AbstractComponent
    {
        /** @var BetweenCondition */
        $self = parent::clone();

        $self->lower = $this->lower;
        $self->column = $this->column;
        $self->higher = $this->higher;

        return $self;
    }
}
