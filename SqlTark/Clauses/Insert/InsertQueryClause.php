<?php
namespace SqlTark\Clauses\Insert;

use SqlTark\Query\Query;
use SqlTark\Clauses\AbstractClause;

class InsertQueryClause extends AbstractInsertClause
{
    /**
     * @var Query $query
     */
    protected $query;

    /**
     * @var string[] $columns
     */
    protected $columns;

    public function getQuery(): Query
    {
        return $this->query;
    }

    public function setQuery(Query $value)
    {
        $this->query = clone $value;
    }

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
     * @return InsertQueryClause
     */
    public function clone(): AbstractClause
    {
        /** @var InsertQueryClause */
        $self = parent::clone();
        
        $self->query = clone $this->query;
        $self->columns = $this->columns;

        return $self;
    }
}