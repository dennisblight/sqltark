<?php

declare(strict_types=1);

namespace SqlTark;

use SqlTark\Expressions\Column;
use SqlTark\Expressions\Literal;
use SqlTark\Expressions\Variable;

final class Expressions
{
    private function __construct()
    {
    }

    public static function column(string $name): Column
    {
        return new Column($name);
    }

    public static function variable(string $name): Variable
    {
        return new Variable($name);
    }

    public static function literal($value): Literal
    {
        return new Literal($value);
    }
}
