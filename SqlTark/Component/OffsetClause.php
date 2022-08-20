<?php
declare(strict_types = 1);
namespace SqlTark\Component;

class OffsetClause extends AbstractComponent
{
    /**
     * @var int $offset
     */
    protected $offset = 0;

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $value)
    {
        $this->offset = $value < 0 ? 0 : $value;
    }

    public function hasOffset(): bool
    {
        return $this->offset > 0;
    }

    /**
     * @return OffsetClause
     */
    public function clone()
    {
        /** @var OffsetClause */
        $self = parent::clone();

        $self->offset = $this->offset;

        return $self;
    }
}