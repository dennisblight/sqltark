<?php
namespace SqlTark\Clauses\Condition;

use InvalidArgumentException;
use SqlTark\Clauses\AbstractClause;

class BasicStringCondition extends BasicCondition
{
    /**
     * @var bool $caseSensitive
     */
    protected $caseSensitive = false;
    
    /**
     * @var string $escapeCharacter
     */
    protected $escapeCharacter;

    public function getCaseSensitive(): bool
    {
        return $this->caseSensitive;
    }

    public function setCaseSensitive(bool $value)
    {
        $this->caseSensitive = $value;
    }

    public function getEscapeCharacter(): string
    {
        return $this->escapeCharacter;
    }

    public function setEscapeCharacter(string $value)
    {
        if(empty($value))
        {
            $value = null;
        }
        elseif(strlen($value) > 1)
        {
            throw new InvalidArgumentException("The EscapeCharacter can only contain a single character!");
        }

        $this->escapeCharacter = $value;
    }

    /**
     * @return BasicStringCondition
     */
    public function clone(): AbstractClause
    {
        /** @var BasicStringCondition */
        $self = parent::clone();

        $self->caseSensitive = $this->caseSensitive;
        $self->escapeCharacter = $this->escapeCharacter;

        return $self;
    }
}