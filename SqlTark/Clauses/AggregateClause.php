<?php
namespace SqlTark\Clauses;

class AggregateClause extends AbstractClause
{
    /**
     * @var string[] $columns
     */
    protected $columns;

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @return string[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param string[] $value
     */
    public function setColumns(array $value)
    {
        $this->columns = $value;
    }

    /**
     * @return AggregateClause
     */
    public function clone(): AbstractClause
    {
        /** @var AggregateClause */
        $self = parent::clone();

        $self->columns = $this->columns;
        $self->type = $this->type;

        return $self;
    }
}