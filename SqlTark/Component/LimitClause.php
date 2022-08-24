<?php
declare(strict_types = 1);
namespace SqlTark\Component;

class LimitClause extends AbstractComponent
{
    /**
     * @var int $limit
     */
    protected $limit = 0;

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $value)
    {
        $this->limit = $value < 0 ? 0 : $value;
    }

    public function hasLimit(): bool
    {
        return $this->limit > 0;
    }

    /**
     * @return static Clone of current object
     */
    public function clone()
    {
        $self = parent::clone();

        $self->limit = $this->limit;

        return $self;
    }
}