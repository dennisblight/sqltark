<?php

declare(strict_types=1);

namespace SqlTark\Component;

class AdHocTableFromClause extends AbstractFrom
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

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $value)
    {
        $this->values = $value;
    }

    /**
     * @return AdHocTableFromClause
     */
    public function clone(): AbstractComponent
    {
        /** @var AdHocTableFromClause */
        $self = parent::clone();

        $self->columns = $this->columns;
        $self->values = $this->values;

        return $self;
    }
}
