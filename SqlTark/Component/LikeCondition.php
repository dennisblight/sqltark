<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Query\Query;
use SqlTark\Expressions\BaseExpression;

class LikeCondition extends AbstractCondition
{
    /**
     * @var BaseExpression|Query $left
     */
    protected $left = null;

    /**
     * @var int $type
     */
    protected $type = LikeType::Like;

    /**
     * @var BaseExpression|Query $right
     */
    protected $right = null;

    /**
     * @var bool $caseSensitive
     */
    protected $caseSensitive = false;

    /**
     * @var ?string $escapeCharacter
     */
    protected $escapeCharacter;

    public function isCaseSensitive(): bool
    {
        return $this->caseSensitive;
    }

    public function setCaseSensitive(bool $value)
    {
        $this->caseSensitive = $value;
    }

    public function getEscapeCharacter(): ?string
    {
        return $this->escapeCharacter;
    }

    public function setEscapeCharacter(?string $value)
    {
        $this->escapeCharacter = $value;
    }

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

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $value)
    {
        $this->type = $value;
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
     * @return LikeCondition
     */
    public function clone(): AbstractComponent
    {
        /** @var LikeCondition */
        $self = parent::clone();

        $self->left = $this->left instanceof Query
            ? clone $this->left
            : $this->left;

        $self->type = $this->type;
        
        $self->right = $this->right instanceof Query
            ? clone $this->right
            : $this->right;

        $self->caseSensitive = $this->caseSensitive;
        $self->escapeCharacter = $this->escapeCharacter;

        return $self;
    }
}
