<?php

declare(strict_types=1);

namespace SqlTark\Component;

use SqlTark\Helper;
use SqlTark\Query\Condition;

class GroupCondition extends AbstractCondition
{
    /**
     * @var Condition $condition
     */
    protected $condition;

    public function getCondition(): Condition
    {
        return $this->condition;
    }

    public function setCondition(Condition $value)
    {
        $this->condition = $value;
    }

    public function __clone()
    {
        $this->condition = Helper::cloneObject($this->condition);
    }
}
