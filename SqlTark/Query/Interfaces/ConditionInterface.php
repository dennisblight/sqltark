<?php

declare(strict_types=1);

namespace SqlTark\Query\Interfaces;

interface ConditionInterface extends QueryInterface
{
    /**
     * Where or condtition
     * @return $this Self object
     */
    public function or(): ConditionInterface;

    /**
     * Where not condtition
     * @param bool $value Is not?
     * @return $this Self object
     */
    public function not(bool $value = true): ConditionInterface;

    /**
     * Where compare two value using ```and``` clause
     * @return $this Self object
     */
    public function where($left, $operator = null, $right = null): ConditionInterface;

    /**
     * Where compare two value using ```or``` clause
     * @return $this Self object
     */
    public function orWhere($left, $operator = null, $right = null): ConditionInterface;

    /**
     * Where compare two value using ```not``` clause
     * @return $this Self object
     */
    public function whereNot($left, $operator = null, $right = null): ConditionInterface;

    /**
     * Where compare two value ```or``` and ```not``` clause
     * @return $this Self object
     */
    public function orWhereNot($left, $operator = null, $right = null): ConditionInterface;

    /**
     * Where value is in values using ```and``` clause
     * @return $this Self object
     */
    public function whereIn($column, $values): ConditionInterface;

    /**
     * Where value is in values using ```or``` clause
     * @return $this Self object
     */
    public function orWhereIn($column, $values): ConditionInterface;

    /**
     * Where value is in values using ```not``` clause
     * @return $this Self object
     */
    public function whereNotIn($column, $values): ConditionInterface;

    /**
     * Where value is in values using ```or``` and ```not``` clause
     * @return $this Self object
     */
    public function orWhereNotIn($column, $values): ConditionInterface;

    /**
     * Where raw expression using ```and``` clause
     * @return $this Self object
     */
    public function whereRaw(string $expression, ...$bindings): ConditionInterface;

    /**
     * Where raw expression using ```or``` clause
     * @return $this Self object
     */
    public function orWhereRaw(string $expression, ...$bindings): ConditionInterface;

    /**
     * Where value is null using ```and``` clause
     * @return $this Self object
     */
    public function whereNull($column): ConditionInterface;

    /**
     * Where value is null using ```or``` clause
     * @return $this Self object
     */
    public function orWhereNull($column): ConditionInterface;

    /**
     * Where value is not null using ```and``` clause
     * @return $this Self object
     */
    public function whereNotNull($column): ConditionInterface;

    /**
     * Where value is not null using ```or``` clause
     * @return $this Self object
     */
    public function orWhereNotNull($column): ConditionInterface;

    /**
     * Where value like using ```and``` clause
     * @return $this Self object
     */
    public function whereLike($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Where value like using ```or``` clause
     * @return $this Self object
     */
    public function orWhereLike($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Where value like using ```not``` clause
     * @return $this Self object
     */
    public function whereNotLike($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Where value like using ```or``` and ```not``` clause
     * @return $this Self object
     */
    public function orWhereNotLike($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Where value starts with using ```and``` clause
     * @return $this Self object
     */
    public function whereStarts($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Where value starts with using ```or``` clause
     * @return $this Self object
     */
    public function orWhereStarts($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Where value starts with using ```not``` clause
     * @return $this Self object
     */
    public function whereNotStarts($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Where value starts with using ```or``` and ```not``` clause
     * @return $this Self object
     */
    public function orWhereNotStarts($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Where value ends with using ```and``` clause
     * @return $this Self object
     */
    public function whereEnds($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Where value ends with using ```or``` clause
     * @return $this Self object
     */
    public function orWhereEnds($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Where value ends with using ```not``` clause
     * @return $this Self object
     */
    public function whereNotEnds($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Where value ends with using ```or``` and ```not``` clause
     * @return $this Self object
     */
    public function orWhereNotEnds($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Where value contains using ```and``` clause
     * @return $this Self object
     */
    public function whereContains($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Where value contains using ```or``` clause
     * @return $this Self object
     */
    public function orWhereContains($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Where value contains using ```not``` clause
     * @return $this Self object
     */
    public function whereNotContains($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Where value contains using ```or``` and ```not``` clause
     * @return $this Self object
     */
    public function orWhereNotContains($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Where value is between two values using ```and``` clause
     * @return $this Self object
     */
    public function whereBetween($column, $lower, $higher): ConditionInterface;

    /**
     * Where value is between two values using ```or``` clause
     * @return $this Self object
     */
    public function orWhereBetween($column, $lower, $higher): ConditionInterface;

    /**
     * Where value is between two values using ```not``` clause
     * @return $this Self object
     */
    public function whereNotBetween($column, $lower, $higher): ConditionInterface;

    /**
     * Where value is between two values using ```or``` and ```not``` clause
     * @return $this Self object
     */
    public function orWhereNotBetween($column, $lower, $higher): ConditionInterface;

    /**
     * Perform grouping condition using ```and``` clause
     * @return $this Self object
     */
    public function whereGroup($group): ConditionInterface;

    /**
     * Perform grouping condition using ```or``` clause
     * @return $this Self object
     */
    public function orWhereGroup($group): ConditionInterface;

    /**
     * Where values in subquery is exists condition using ```and``` clause
     * @return $this Self object
     */
    public function whereExists($query): ConditionInterface;

    /**
     * Where values in subquery is exists condition using ```or``` clause
     * @return $this Self object
     */
    public function orWhereExists($query): ConditionInterface;

    /**
     * Where values in subquery ```not``` exists condition using ```and``` clause
     * @return $this Self object
     */
    public function whereNotExists($query): ConditionInterface;

    /**
     * Where values in subquery is ```not``` exists condition using ```or``` clause
     * @return $this Self object
     */
    public function orWhereNotExists($query): ConditionInterface;
}
