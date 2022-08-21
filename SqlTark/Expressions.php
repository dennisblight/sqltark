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

    public static function column(string $name, ?string $wrap = null): Column
    {
        $result = new Column($name);
        return $result->wrap($wrap);
    }

    public static function variable(string $name, ?string $wrap = null): Variable
    {
        $result = new Variable($name);
        return $result->wrap($wrap);
    }

    public static function literal($value, ?string $wrap = null): Literal
    {
        $result = new Literal($value);
        return $result->wrap($wrap);
    }
}
