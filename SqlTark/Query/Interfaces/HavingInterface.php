<?php

declare(strict_types=1);

namespace SqlTark\Query\Interfaces;

interface HavingInterface extends ConditionInterface
{
    /**
     * Having compare two value using ```and``` clause
     * @return static Self object
     */
    public function having($left, $operator = null, $right = null): ConditionInterface;

    /**
     * Having compare two value using ```or``` clause
     * @return static Self object
     */
    public function orHaving($left, $operator = null, $right = null): ConditionInterface;

    /**
     * Having compare two value using ```not``` clause
     * @return static Self object
     */
    public function havingNot($left, $operator = null, $right = null): ConditionInterface;

    /**
     * Having compare two value ```or``` and ```not``` clause
     * @return static Self object
     */
    public function orHavingNot($left, $operator = null, $right = null): ConditionInterface;

    /**
     * Having value is in values using ```and``` clause
     * @return static Self object
     */
    public function havingIn($column, $values): ConditionInterface;

    /**
     * Having value is in values using ```or``` clause
     * @return static Self object
     */
    public function orHavingIn($column, $values): ConditionInterface;

    /**
     * Having value is in values using ```not``` clause
     * @return static Self object
     */
    public function havingNotIn($column, $values): ConditionInterface;

    /**
     * Having value is in values using ```or``` and ```not``` clause
     * @return static Self object
     */
    public function orHavingNotIn($column, $values): ConditionInterface;

    /**
     * Having raw expression using ```and``` clause
     * @return static Self object
     */
    public function havingRaw(string $expression, ...$bindings): ConditionInterface;

    /**
     * Having raw expression using ```or``` clause
     * @return static Self object
     */
    public function orHavingRaw(string $expression, ...$bindings): ConditionInterface;

    /**
     * Having value is null using ```and``` clause
     * @return static Self object
     */
    public function havingNull($column): ConditionInterface;

    /**
     * Having value is null using ```or``` clause
     * @return static Self object
     */
    public function orHavingNull($column): ConditionInterface;

    /**
     * Having value is not null using ```and``` clause
     * @return static Self object
     */
    public function havingNotNull($column): ConditionInterface;

    /**
     * Having value is not null using ```or``` clause
     * @return static Self object
     */
    public function orHavingNotNull($column): ConditionInterface;

    /**
     * Having value like using ```and``` clause
     * @return static Self object
     */
    public function havingLike($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Having value like using ```or``` clause
     * @return static Self object
     */
    public function orHavingLike($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Having value like using ```not``` clause
     * @return static Self object
     */
    public function havingNotLike($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Having value like using ```or``` and ```not``` clause
     * @return static Self object
     */
    public function orHavingNotLike($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Having value starts with using ```and``` clause
     * @return static Self object
     */
    public function havingStarts($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Having value starts with using ```or``` clause
     * @return static Self object
     */
    public function orHavingStarts($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Having value starts with using ```not``` clause
     * @return static Self object
     */
    public function havingNotStarts($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Having value starts with using ```or``` and ```not``` clause
     * @return static Self object
     */
    public function orHavingNotStarts($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Having value ends with using ```and``` clause
     * @return static Self object
     */
    public function havingEnds($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Having value ends with using ```or``` clause
     * @return static Self object
     */
    public function orHavingEnds($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Having value ends with using ```not``` clause
     * @return static Self object
     */
    public function havingNotEnds($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Having value ends with using ```or``` and ```not``` clause
     * @return static Self object
     */
    public function orHavingNotEnds($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Having value contains using ```and``` clause
     * @return static Self object
     */
    public function havingContains($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Having value contains using ```or``` clause
     * @return static Self object
     */
    public function orHavingContains($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Having value contains using ```not``` clause
     * @return static Self object
     */
    public function havingNotContains($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Having value contains using ```or``` and ```not``` clause
     * @return static Self object
     */
    public function orHavingNotContains($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface;

    /**
     * Having value is between two values using ```and``` clause
     * @return static Self object
     */
    public function havingBetween($column, $lower, $higher): ConditionInterface;

    /**
     * Having value is between two values using ```or``` clause
     * @return static Self object
     */
    public function orHavingBetween($column, $lower, $higher): ConditionInterface;

    /**
     * Having value is between two values using ```not``` clause
     * @return static Self object
     */
    public function havingNotBetween($column, $lower, $higher): ConditionInterface;

    /**
     * Having value is between two values using ```or``` and ```not``` clause
     * @return static Self object
     */
    public function orHavingNotBetween($column, $lower, $higher): ConditionInterface;

    /**
     * Perform grouping condition using ```and``` clause
     * @return static Self object
     */
    public function havingGroup($group): ConditionInterface;

    /**
     * Perform grouping condition using ```or``` clause
     * @return static Self object
     */
    public function orHavingGroup($group): ConditionInterface;

    /**
     * Having values in subquery is exists condition using ```and``` clause
     * @return static Self object
     */
    public function havingExists($query): ConditionInterface;

    /**
     * Having values in subquery is exists condition using ```or``` clause
     * @return static Self object
     */
    public function orHavingExists($query): ConditionInterface;

    /**
     * Having values in subquery ```not``` exists condition using ```and``` clause
     * @return static Self object
     */
    public function havingNotExists($query): ConditionInterface;

    /**
     * Having values in subquery is ```not``` exists condition using ```or``` clause
     * @return static Self object
     */
    public function orHavingNotExists($query): ConditionInterface;
}
