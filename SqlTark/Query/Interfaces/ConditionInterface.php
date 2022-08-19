<?php

declare(strict_types=1);

namespace SqlTark\Query\Interfaces;

interface ConditionInterface
{
    public function and();

    public function or();
    
    public function not(bool $value = true);

    public function where($column, $operator, $value = null);

    public function whereIn($column, $values);

    public function whereRaw(string $sql, ...$bindings);

    public function whereNull($column);

    public function whereTrue($column);

    public function whereFalse($column);

    public function whereLike($column, $value, bool $caseSensitive, ?string $escapeCharacter);

    public function whereBetween($column, $lower, $higher);

    public function whereGroup(callable $group);

    public function whereExists($query);
}