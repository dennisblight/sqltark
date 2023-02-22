<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Helper;
use SqlTark\Query\Query;

class FromClause extends AbstractFrom
{
    /**
     * @var string|Query $table
     */
    protected $table;

    /**
     * @return string|Query
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param string|Query $value
     */
    public function setTable($value)
    {
        $this->table = $value;
    }

    public function getAlias(): string
    {
        if(empty($this->alias))
        {
            if(is_string($this->table))
            {
                if (stripos($this->table, ' as ') !== false) {
                    $segments = array_filter(explode(' ', $this->table), function ($item) {
                        return $item != '';
                    });

                    return $segments[2];
                }
            }
            elseif($this->table instanceof Query)
            {
                return $this->table->getAlias();
            }
        }

        return $this->alias;
    }

    public function __clone()
    {
        $this->table = Helper::cloneObject($this->table);
    }
}
