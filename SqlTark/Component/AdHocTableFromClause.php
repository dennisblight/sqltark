<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SplFixedArray;
use SqlTark\Expressions\BaseExpression;
use SqlTark\Helper;
use SqlTark\Query\Query;

class AdHocTableFromClause extends AbstractFrom
{
    /**
     * @var SplFixedArray<string> $columns
     */
    protected $columns;

    /**
     * @var SplFixedArray<SplFixedArray<BaseExpression|Query>> $values
     */
    protected $values;

    /**
     * @return SplFixedArray<string>
     */
    public function getColumns(): iterable
    {
        return $this->columns;
    }

    /**
     * @param SplFixedArray<string> $value
     */
    public function setColumns(iterable $value)
    {
        $this->columns = $value;
    }

    /**
     * @return SplFixedArray<SplFixedArray<BaseExpression|Query>>
     */
    public function getValues(): iterable
    {
        return $this->values;
    }

    /**
     * @param SplFixedArray<SplFixedArray<BaseExpression|Query>> $value
     */
    public function setValues(iterable $value)
    {
        $this->values = $value;
    }

    public function __clone()
    {
        $this->columns = Helper::cloneObject($this->columns);
        $this->values = Helper::cloneObject($this->values);
    }
}
