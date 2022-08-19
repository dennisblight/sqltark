<?php
declare(strict_types=1);

namespace SqlTark\Query\Traits;

use InvalidArgumentException;
use SqlTark\Expressions;
use SqlTark\Expressions\BaseExpression;
use SqlTark\Query\Query;

/**
 * @mixin BaseQuery
 */
trait ConditionTrait
{
    /**
     * @var bool $orFlag
     */
    private $orFlag = false;
    
    /**
     * @var bool $notFlag
     */
    private $notFlag = false;

    public function and()
    {
        $this->orFlag = false;
        return $this;
    }

    public function or()
    {
        $this->orFlag = true;
        return $this;
    }

    public function not(bool $value = true)
    {
        $this->notFlag = $value;
        return $this;
    }

    protected function getOr(): bool
    {
        $return = $this->orFlag;

        $this->orFlag = false;
        
        return $return;
    }

    protected function getNot(): bool
    {
        $return = $this->notFlag;

        $this->notFlag = false;
        
        return $return;
    }

    public function where($left, $operator = null, $right = null)
    {
        if(func_num_args() == 1)
        {
            if(is_iterable($left) || is_object($left))
            {
                $orFlag = $this->getOr();
                $notFlag = $this->getNot();
                foreach($left as $column => $value)
                {
                    if($orFlag)
                    {
                        $this->or();
                    }
                    else
                    {
                        $this->and();
                    }

                    $this->not($notFlag)->where($column, '=', $value);
                }
            }
            else
            {
                throw new InvalidArgumentException(
                    "First parameter must be iterable or object"
                );
            }

            return $this;
        }
        elseif(func_num_args() == 2)
        {
            $right = $operator;
            $operator = '=';
        }

        if(is_callable($left))
        {
            $query = new Query;
            $query->setParent($this);
            return $this->where($left($query), $operator, $right);
        }

        if(is_callable($right))
        {
            $query = new Query;
            $query->setParent($this);
            return $this->where($left, $operator, $right($query));
        }
        
        $left = $this->resolveExpression($left, 'left');
        $right = $this->resolveLiteral($right, 'right');

        return $this;
    }

    protected function resolveExpression($expr, string $name)
    {
        if(is_string($expr))
        {
            $expr = Expressions::column($expr);
        }
        elseif(is_int($expr) || is_float($expr) || is_null($expr) || $expr instanceof \DateTime)
        {
            $expr = Expressions::literal($expr);
        }
        elseif(!($expr instanceof BaseExpression) && !($expr instanceof Query))
        {
            $class = get_class($expr);
            throw new InvalidArgumentException(
                "Could not resolve '$class' for $name parameter."
            );
        }

        return $expr;
    }

    protected function resolveLiteral($expr, string $name)
    {
        if(is_string($expr) || is_int($expr) || is_float($expr) || is_null($expr) || $expr instanceof \DateTime)
        {
            $expr = Expressions::literal($expr);
        }
        elseif(!($expr instanceof BaseExpression) && !($expr instanceof Query))
        {
            if(is_object($expr) && method_exists($expr, '__toString'))
            {
                $expr = Expressions::literal((string) $expr);
            }
            else
            {
                $class = get_class($expr);
                throw new InvalidArgumentException(
                    "Could not resolve '$class' for $name parameter."
                );
            }
        }

        return $expr;
    }

    public function orWhere($left, $operator = null, $right = null)
    {
        if(func_num_args() == 1)
        {
            return $this->or()->where($left);
        }
        elseif(func_num_args() == 2)
        {
            return $this->or()->where($left, $operator);
        }

        return $this->or()->where($left, $operator, $right);
    }

    public function whereNot($left, $operator = null, $right = null)
    {
        if(func_num_args() == 1)
        {
            return $this->not()->where($left);
        }
        elseif(func_num_args() == 2)
        {
            return $this->not()->where($left, $operator);
        }

        return $this->not()->where($left, $operator, $right);
    }

    public function orWhereNot($left, $operator = null, $right = null)
    {
        if(func_num_args() == 1)
        {
            return $this->or()->not()->where($left);
        }
        elseif(func_num_args() == 2)
        {
            return $this->or()->not()->where($left, $operator);
        }

        return $this->or()->not()->where($left, $operator, $right);
    }

    public function whereIn($column, $values)
    {
        return $this;
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

    public function whereRaw(string $sql, ...$bindings)
    {
        return $this;
    }

    public function orWhereRaw(string $sql, ...$bindings)
    {
        return $this->or()->whereRaw($sql, ...$bindings);
    }

    public function whereNull($column)
    {
        return $this;
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

    public function whereTrue($column)
    {
        return $this;
    }

    public function orWhereTrue($column)
    {
        return $this->or()->whereTrue($column);
    }

    public function whereFalse($column)
    {
        return $this;
    }

    public function orWhereFalse($column)
    {
        return $this->or()->whereFalse($column);
    }

    public function whereLike($column, $value, bool $caseSensitive, ?string $escapeCharacter)
    {
        return $this;
    }

    public function orWhereLike($column, $value, bool $caseSensitive, ?string $escapeCharacter)
    {
        return $this->or()->whereLike($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereNotLike($column, $value, bool $caseSensitive, ?string $escapeCharacter)
    {
        return $this->not()->whereLike($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orWhereNotLike($column, $value, bool $caseSensitive, ?string $escapeCharacter)
    {
        return $this->or()->not()->whereLike($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereStarts($column, $value, bool $caseSensitive, ?string $escapeCharacter)
    {
        return $this;
    }

    public function orWhereStarts($column, $value, bool $caseSensitive, ?string $escapeCharacter)
    {
        return $this->or()->whereStarts($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereNotStarts($column, $value, bool $caseSensitive, ?string $escapeCharacter)
    {
        return $this->not()->whereStarts($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orWhereNotStarts($column, $value, bool $caseSensitive, ?string $escapeCharacter)
    {
        return $this->or()->not()->whereStarts($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereEnds($column, $value, bool $caseSensitive, ?string $escapeCharacter)
    {
        return $this;
    }

    public function orWhereEnds($column, $value, bool $caseSensitive, ?string $escapeCharacter)
    {
        return $this->or()->whereEnds($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereNotEnds($column, $value, bool $caseSensitive, ?string $escapeCharacter)
    {
        return $this->not()->whereEnds($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orWhereNotEnds($column, $value, bool $caseSensitive, ?string $escapeCharacter)
    {
        return $this->or()->not()->whereEnds($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereContains($column, $value, bool $caseSensitive, ?string $escapeCharacter)
    {
        return $this;
    }

    public function orWhereContains($column, $value, bool $caseSensitive, ?string $escapeCharacter)
    {
        return $this->or()->whereContains($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereNotContains($column, $value, bool $caseSensitive, ?string $escapeCharacter)
    {
        return $this->not()->whereContains($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orWhereNotContains($column, $value, bool $caseSensitive, ?string $escapeCharacter)
    {
        return $this->or()->not()->whereContains($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereBetween($column, $lower, $higher)
    {
        return $this;
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

    public function whereGroup(callable $group)
    {
        return $this;
    }

    public function orWhereGroup(callable $group)
    {
        return $this->or()->whereGroup($group);
    }

    public function whereExists($query)
    {
        return $this;
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