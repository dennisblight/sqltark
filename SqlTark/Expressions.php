<?php

declare(strict_types=1);

namespace SqlTark;

use SqlTark\Expressions\Column;
use SqlTark\Expressions\Literal;
use SqlTark\Expressions\Variable;
use SqlTark\Expressions\Raw;

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

    public static function variable(?string $name = null, ?string $wrap = null): Variable
    {
        $result = new Variable($name);
        return $result->wrap($wrap);
    }

    public static function literal($value, ?string $wrap = null): Literal
    {
        $result = new Literal($value);
        return $result->wrap($wrap);
    }

    public static function raw(string $expression): Raw
    {
        return new Raw($expression);
    }
}

namespace SqlTark\Expressions;

function literal($value, ?string $wrap = null): Literal
{
    $result = new Literal($value);
    return $result->wrap($wrap);
}

function column(string $name, ?string $wrap = null): Column
{
    $result = new Column($name);
    return $result->wrap($wrap);
}

function variable(?string $name = null, ?string $wrap = null): Variable
{
    $result = new Variable($name);
    return $result->wrap($wrap);
}

function raw(string $expression, ?iterable $bindings = []): Raw
{
    return new Raw($expression, $bindings);
}