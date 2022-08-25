<?php

declare(strict_types=1);

namespace SqlTark\Query\Traits;

use SqlTark\Component\LikeType;

trait ConditionTrait
{
    use BaseConditionTrait;

    public function where($left, $operator = null, $right = null)
    {
        return call_user_func_array([$this->withWhere(), 'condition'], func_get_args());
    }

    public function orWhere($left, $operator = null, $right = null)
    {
        return call_user_func_array([$this->or(), 'where'], func_get_args());
    }

    public function whereNot($left, $operator = null, $right = null)
    {
        return call_user_func_array([$this->not(), 'where'], func_get_args());
    }

    public function orWhereNot($left, $operator = null, $right = null)
    {
        return call_user_func_array([$this->or()->not(), 'where'], func_get_args());
    }

    public function whereIn($column, $values)
    {
        return call_user_func_array([$this->withWhere(), 'conditionIn'], func_get_args());
    }

    public function orWhereIn($column, $values)
    {
        return $this->or()->whereIn($column, $values);
    }

    public function whereNotIn($column, $values)
    {
        return $this->not()->whereIn($column, $values);
    }

    public function orWhereNotIn($column, $values)
    {
        return $this->or()->not()->whereIn($column, $values);
    }

    public function whereRaw(string $expression, ...$bindings)
    {
        return call_user_func_array([$this->withWhere(), 'conditionRaw'], func_get_args());
    }

    public function orWhereRaw(string $expression, ...$bindings)
    {
        return $this->or()->whereRaw($expression, ...$bindings);
    }

    public function whereNull($column)
    {
        return call_user_func_array([$this->withWhere(), 'conditionNull'], func_get_args());
    }

    public function orWhereNull($column)
    {
        return $this->or()->whereNull($column);
    }

    public function whereNotNull($column)
    {
        return $this->not()->whereNull($column);
    }

    public function orWhereNotNull($column)
    {
        return $this->or()->not()->whereNull($column);
    }

    public function whereLike($column, string $value, bool $caseSensitive = false, ?string $escapeCharacter = null)
    {
        return $this->withWhere()->conditionLike($column, $value, $caseSensitive, $escapeCharacter, LikeType::Like);
    }

    public function orWhereLike($column, string $value, bool $caseSensitive = false, ?string $escapeCharacter = null)
    {
        return $this->or()->whereLike($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereNotLike($column, string $value, bool $caseSensitive = false, ?string $escapeCharacter = null)
    {
        return $this->not()->whereLike($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orWhereNotLike($column, string $value, bool $caseSensitive = false, ?string $escapeCharacter = null)
    {
        return $this->or()->not()->whereLike($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereStarts($column, string $value, bool $caseSensitive = false, ?string $escapeCharacter = null)
    {
        return $this->withWhere()->conditionLike($column, $value, $caseSensitive, $escapeCharacter, LikeType::Starts);
    }

    public function orWhereStarts($column, string $value, bool $caseSensitive = false, ?string $escapeCharacter = null)
    {
        return $this->or()->whereStarts($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereNotStarts($column, string $value, bool $caseSensitive = false, ?string $escapeCharacter = null)
    {
        return $this->not()->whereStarts($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orWhereNotStarts($column, string $value, bool $caseSensitive = false, ?string $escapeCharacter = null)
    {
        return $this->or()->not()->whereStarts($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereEnds($column, string $value, bool $caseSensitive = false, ?string $escapeCharacter = null)
    {
        return $this->withWhere()->conditionLike($column, $value, $caseSensitive, $escapeCharacter, LikeType::Ends);
    }

    public function orWhereEnds($column, string $value, bool $caseSensitive = false, ?string $escapeCharacter = null)
    {
        return $this->or()->whereEnds($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereNotEnds($column, string $value, bool $caseSensitive = false, ?string $escapeCharacter = null)
    {
        return $this->not()->whereEnds($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orWhereNotEnds($column, string $value, bool $caseSensitive = false, ?string $escapeCharacter = null)
    {
        return $this->or()->not()->whereEnds($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereContains($column, string $value, bool $caseSensitive = false, ?string $escapeCharacter = null)
    {
        return $this->withWhere()->conditionLike($column, $value, $caseSensitive, $escapeCharacter, LikeType::Contains);
    }

    public function orWhereContains($column, string $value, bool $caseSensitive = false, ?string $escapeCharacter = null)
    {
        return $this->or()->whereContains($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereNotContains($column, string $value, bool $caseSensitive = false, ?string $escapeCharacter = null)
    {
        return $this->not()->whereContains($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orWhereNotContains($column, string $value, bool $caseSensitive = false, ?string $escapeCharacter = null)
    {
        return $this->or()->not()->whereContains($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereBetween($column, $lower, $higher)
    {
        return call_user_func_array([$this->withWhere(), 'conditionBetween'], func_get_args());
    }

    public function orWhereBetween($column, $lower, $higher)
    {
        return $this->or()->whereBetween($column, $lower, $higher);
    }

    public function whereNotBetween($column, $lower, $higher)
    {
        return $this->not()->whereBetween($column, $lower, $higher);
    }

    public function orWhereNotBetween($column, $lower, $higher)
    {
        return $this->or()->not()->whereBetween($column, $lower, $higher);
    }
    
    public function whereGroup($group)
    {
        return call_user_func_array([$this->withWhere(), 'conditionGroup'], func_get_args());
    }

    public function orWhereGroup($group)
    {
        return $this->or()->whereGroup($group);
    }

    public function whereExists($query)
    {
        return call_user_func_array([$this->withWhere(), 'conditionExists'], func_get_args());
    }

    public function orWhereExists($query)
    {
        return $this->or()->whereExists($query);
    }

    public function whereNotExists($query)
    {
        return $this->not()->whereExists($query);
    }

    public function orWhereNotExists($query)
    {
        return $this->or()->not()->whereExists($query);
    }
}
