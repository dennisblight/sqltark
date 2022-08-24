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
     * @return static Clone of current object
     */
    public function clone()
    {
        $self = parent::clone();

        $self->lower = $this->lower instanceof Query
            ? clone $this->lower
            : $this->lower;
            
        $self->column = $this->column instanceof Query
            ? clone $this->column
            : $this->column;

        $self->higher = $this->higher instanceof Query
            ? clone $this->higher
            : $this->higher;

        return $self;
    }
}
