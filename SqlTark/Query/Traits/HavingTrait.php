<?php

declare(strict_types=1);

namespace SqlTark\Query\Traits;

use SqlTark\Component\LikeType;
use SqlTark\Query\Interfaces\ConditionInterface;

trait HavingTrait
{
    use BaseConditionTrait;

    public function having($left, $operator = null, $right = null): ConditionInterface
    {
        return call_user_func_array([$this->withHaving(), 'condition'], func_get_args());
    }

    public function orHaving($left, $operator = null, $right = null): ConditionInterface
    {
        return call_user_func_array([$this->or(), 'having'], func_get_args());
    }

    public function havingNot($left, $operator = null, $right = null): ConditionInterface
    {
        return call_user_func_array([$this->not(), 'having'], func_get_args());
    }

    public function orHavingNot($left, $operator = null, $right = null): ConditionInterface
    {
        return call_user_func_array([$this->or()->not(), 'having'], func_get_args());
    }

    public function havingIn($column, $values): ConditionInterface
    {
        return call_user_func_array([$this->withHaving(), 'conditionIn'], func_get_args());
    }

    public function orHavingIn($column, $values): ConditionInterface
    {
        return $this->or()->havingIn($column, $values);
    }

    public function havingNotIn($column, $values): ConditionInterface
    {
        return $this->not()->havingIn($column, $values);
    }

    public function orHavingNotIn($column, $values): ConditionInterface
    {
        return $this->or()->not()->havingIn($column, $values);
    }

    public function havingRaw(string $expression, ...$bindings): ConditionInterface
    {
        return call_user_func_array([$this->withHaving(), 'conditionRaw'], func_get_args());
    }

    public function orHavingRaw(string $expression, ...$bindings): ConditionInterface
    {
        return $this->or()->havingRaw($expression, ...$bindings);
    }

    public function havingNull($column): ConditionInterface
    {
        return call_user_func_array([$this->withHaving(), 'conditionNull'], func_get_args());
    }

    public function orHavingNull($column): ConditionInterface
    {
        return $this->or()->havingNull($column);
    }

    public function havingNotNull($column): ConditionInterface
    {
        return $this->not()->havingNull($column);
    }

    public function orHavingNotNull($column): ConditionInterface
    {
        return $this->or()->not()->havingNull($column);
    }

    public function havingLike($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->withHaving()->conditionLike($column, $value, $caseSensitive, $escapeCharacter, LikeType::Like);
    }

    public function orHavingLike($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->havingLike($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function havingNotLike($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->not()->havingLike($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orHavingNotLike($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->not()->havingLike($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function havingStarts($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->withHaving()->conditionLike($column, $value, $caseSensitive, $escapeCharacter, LikeType::Starts);
    }

    public function orHavingStarts($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->havingStarts($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function havingNotStarts($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->not()->havingStarts($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orHavingNotStarts($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->not()->havingStarts($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function havingEnds($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->withHaving()->conditionLike($column, $value, $caseSensitive, $escapeCharacter, LikeType::Ends);
    }

    public function orHavingEnds($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->havingEnds($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function havingNotEnds($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->not()->havingEnds($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orHavingNotEnds($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->not()->havingEnds($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function havingContains($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->withHaving()->conditionLike($column, $value, $caseSensitive, $escapeCharacter, LikeType::Contains);
    }

    public function orHavingContains($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->havingContains($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function havingNotContains($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->not()->havingContains($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orHavingNotContains($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->not()->havingContains($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function havingBetween($column, $lower, $higher): ConditionInterface
    {
        return call_user_func_array([$this->withHaving(), 'conditionBetween'], func_get_args());
    }

    public function orHavingBetween($column, $lower, $higher): ConditionInterface
    {
        return $this->or()->havingBetween($column, $lower, $higher);
    }

    public function havingNotBetween($column, $lower, $higher): ConditionInterface
    {
        return $this->not()->havingBetween($column, $lower, $higher);
    }

    public function orHavingNotBetween($column, $lower, $higher): ConditionInterface
    {
        return $this->or()->not()->havingBetween($column, $lower, $higher);
    }

    public function havingGroup($group): ConditionInterface
    {
        return call_user_func_array([$this->withHaving(), 'conditionGroup'], func_get_args());
    }

    public function orHavingGroup($group): ConditionInterface
    {
        return $this->or()->havingGroup($group);
    }

    public function havingExists($query): ConditionInterface
    {
        return call_user_func_array([$this->withHaving(), 'conditionExists'], func_get_args());
    }

    public function orHavingExists($query): ConditionInterface
    {
        return $this->or()->havingExists($query);
    }

    public function havingNotExists($query): ConditionInterface
    {
        return $this->not()->havingExists($query);
    }

    public function orHavingNotExists($query): ConditionInterface
    {
        return $this->or()->not()->havingExists($query);
    }
}
