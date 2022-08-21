<?php

declare(strict_types=1);

namespace SqlTark\Query;

use SqlTark\Query\Interfaces\ConditionInterface;
use SqlTark\Query\Traits\ConditionTrait;

class Condition extends BaseQuery implements ConditionInterface
{
    use ConditionTrait;
}
