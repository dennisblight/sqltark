<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Query\Query;
use SqlTark\Expressions\BaseExpression;

class LikeCondition extends AbstractCondition
{
    /**
     * @var BaseExpression|Query $column
     */
    protected $column = null;

    /**
     * @var int $type
     */
    protected $type = LikeType::Like;

    /**
     * @var string $value
     */
    protected $value;

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

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $value)
    {
        $this->type = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return static Clone of current object
     */
    public function clone()
    {
        $self = parent::clone();

        $self->column = $this->column instanceof Query
            ? clone $this->column
            : $this->column;

        $self->type = $this->type;
        
        $self->value = $this->value;

        $self->caseSensitive = $this->caseSensitive;
        $self->escapeCharacter = $this->escapeCharacter;

        return $self;
    }
}
