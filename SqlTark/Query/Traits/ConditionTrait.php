<?php

declare(strict_types=1);

namespace SqlTark\Query\Traits;

use SqlTark\Component\LikeType;
use SqlTark\Query\Interfaces\ConditionInterface;

trait ConditionTrait
{
    use BaseConditionTrait;

    public function where($left, $operator = null, $right = null): ConditionInterface
    {
        return call_user_func_array([$this->withWhere(), 'condition'], func_get_args());
    }

    public function orWhere($left, $operator = null, $right = null): ConditionInterface
    {
        return call_user_func_array([$this->or(), 'where'], func_get_args());
    }

    public function whereNot($left, $operator = null, $right = null): ConditionInterface
    {
        return call_user_func_array([$this->not(), 'where'], func_get_args());
    }

    public function orWhereNot($left, $operator = null, $right = null): ConditionInterface
    {
        return call_user_func_array([$this->or()->not(), 'where'], func_get_args());
    }

    public function whereIn($column, $values): ConditionInterface
    {
        return call_user_func_array([$this->withWhere(), 'conditionIn'], func_get_args());
    }

    public function orWhereIn($column, $values): ConditionInterface
    {
        return $this->or()->whereIn($column, $values);
    }

    public function whereNotIn($column, $values): ConditionInterface
    {
        return $this->not()->whereIn($column, $values);
    }

    public function orWhereNotIn($column, $values): ConditionInterface
    {
        return $this->or()->not()->whereIn($column, $values);
    }

    public function whereRaw(string $expression, ...$bindings): ConditionInterface
    {
        return call_user_func_array([$this->withWhere(), 'conditionRaw'], func_get_args());
    }

    public function orWhereRaw(string $expression, ...$bindings): ConditionInterface
    {
        return $this->or()->whereRaw($expression, ...$bindings);
    }

    public function whereNull($column): ConditionInterface
    {
        return call_user_func_array([$this->withWhere(), 'conditionNull'], func_get_args());
    }

    public function orWhereNull($column): ConditionInterface
    {
        return $this->or()->whereNull($column);
    }

    public function whereNotNull($column): ConditionInterface
    {
        return $this->not()->whereNull($column);
    }

    public function orWhereNotNull($column): ConditionInterface
    {
        return $this->or()->not()->whereNull($column);
    }

    public function whereLike($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->withWhere()->conditionLike($column, $value, $caseSensitive, $escapeCharacter, LikeType::Like);
    }

    public function orWhereLike($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->whereLike($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereNotLike($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->not()->whereLike($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orWhereNotLike($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->not()->whereLike($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereStarts($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->withWhere()->conditionLike($column, $value, $caseSensitive, $escapeCharacter, LikeType::Starts);
    }

    public function orWhereStarts($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->whereStarts($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereNotStarts($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->not()->whereStarts($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orWhereNotStarts($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->not()->whereStarts($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereEnds($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->withWhere()->conditionLike($column, $value, $caseSensitive, $escapeCharacter, LikeType::Ends);
    }

    public function orWhereEnds($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->whereEnds($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereNotEnds($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->not()->whereEnds($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orWhereNotEnds($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->not()->whereEnds($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereContains($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->withWhere()->conditionLike($column, $value, $caseSensitive, $escapeCharacter, LikeType::Contains);
    }

    public function orWhereContains($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->whereContains($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereNotContains($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->not()->whereContains($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orWhereNotContains($column, string $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->not()->whereContains($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereBetween($column, $lower, $higher): ConditionInterface
    {
        return call_user_func_array([$this->withWhere(), 'conditionBetween'], func_get_args());
    }

    public function orWhereBetween($column, $lower, $higher): ConditionInterface
    {
        return $this->or()->whereBetween($column, $lower, $higher);
    }

    public function whereNotBetween($column, $lower, $higher): ConditionInterface
    {
        return $this->not()->whereBetween($column, $lower, $higher);
    }

    public function orWhereNotBetween($column, $lower, $higher): ConditionInterface
    {
        return $this->or()->not()->whereBetween($column, $lower, $higher);
    }
    
    public function whereGroup($group): ConditionInterface
    {
        return call_user_func_array([$this->withWhere(), 'conditionGroup'], func_get_args());
    }

    public function orWhereGroup($group): ConditionInterface
    {
        return $this->or()->whereGroup($group);
    }

    public function whereExists($query): ConditionInterface
    {
        return call_user_func_array([$this->withWhere(), 'conditionExists'], func_get_args());
    }

    public function orWhereExists($query): ConditionInterface
    {
        return $this->or()->whereExists($query);
    }

    public function whereNotExists($query): ConditionInterface
    {
        return $this->not()->whereExists($query);
    }

    public function orWhereNotExists($query): ConditionInterface
    {
        return $this->or()->not()->whereExists($query);
    }
}
