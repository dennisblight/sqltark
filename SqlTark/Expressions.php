<?php
namespace SqlTark;

final class Expressions
{
    private function __construct() { }

    public static function variable(string $name): Variable
    {
        return new Variable($name);
    }

    public static function unsafeLiteral(string $value, bool $replaceQuotes = true): UnsafeLiteral
    {
        return new UnsafeLiteral($value, $replaceQuotes);
    }
}