<?php

declare(strict_types=1);

namespace SqlTark\Clauses\Insert;

use SqlTark\Clauses\AbstractClause;

class InsertClause extends AbstractInsertClause
{
    /**
     * @var string[] $columns
     */
    protected $columns;

    /**
     * @var array $values
     */
    protected $values;

    /**
     * @var bool $returnId
     */
    protected $returnId = false;

    /**
     * @return string[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getReturnId(): bool
    {
        return $this->returnId;
    }

    /**
     * @param string[] $value
     */
    public function setColumns(array $value)
    {
        $this->columns = $value;
    }

    public function setValues(array $value)
    {
        $this->values = $value;
    }

    public function setReturnId(bool $value)
    {
        $this->returnId = $value;
    }

    /**
     * @param InsertClause
     */
    public function clone(): AbstractClause
    {
        /** @var InsertClause */
        $self = parent::clone();

        $self->columns = $this->columns;
        $self->values = $this->values;
        $self->returnId = $this->returnId;

        return $self;
    }
}
