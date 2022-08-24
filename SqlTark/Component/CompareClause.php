<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Expressions\BaseExpression;
use SqlTark\Query\Query;

class CompareClause extends AbstractCondition
{
    /**
     * @var BaseExpression|Query $left
     */
    protected $left = null;

    /**
     * @var string $operator
     */
    protected $operator = null;

    /**
     * @var BaseExpression|Query $right
     */
    protected $right = null;

    /**
     * @return BaseExpression|Query
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @param BaseExpression|Query $value
     */
    public function setLeft($value)
    {
        $this->left = $value;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function setOperator(string $value)
    {
        $this->operator = $value;
    }

    /**
     * @return BaseExpression|Query
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param BaseExpression|Query $value
     */
    public function setRight($value)
    {
        $this->right = $value;
    }

    /**
     * @return static Clone of current object
     */
    public function clone()
    {
        $self = parent::clone();

        $self->left = $this->left instanceof Query
            ? clone $this->left
            : $this->left;

        $self->operator = $this->operator;
        
        $self->right = $this->right instanceof Query
            ? clone $this->right
            : $this->right;

        return $self;
    }
}
