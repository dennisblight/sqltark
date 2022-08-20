<?php

declare(strict_types=1);

namespace SqlTark\Query\Traits;

use SplFixedArray;
use SqlTark\Helper;
use SqlTark\Expressions;
use SqlTark\Query\Query;
use SqlTark\Query\BaseQuery;
use InvalidArgumentException;
use SqlTark\Component\LikeType;
use SqlTark\Component\InCondition;
use SqlTark\Component\RawCondition;
use SqlTark\Component\CompareClause;
use SqlTark\Component\ComponentType;
use SqlTark\Component\LikeCondition;
use SqlTark\Component\NullCondition;
use SqlTark\Component\GroupCondition;
use SqlTark\Component\BetweenCondition;
use SqlTark\Expressions\BaseExpression;
use SqlTark\Query\Interfaces\ConditionInterface;

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

    public function and(): ConditionInterface
    {
        $this->orFlag = false;
        return $this;
    }

    public function or(): ConditionInterface
    {
        $this->orFlag = true;
        return $this;
    }

    public function not(bool $value = true): ConditionInterface
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

    public function where($left, $operator = null, $right = null): ConditionInterface
    {
        if (func_num_args() == 1) {
            if (is_iterable($left) || is_object($left)) {
                $orFlag = $this->getOr();
                $notFlag = $this->getNot();
                foreach ($left as $column => $value) {
                    if ($orFlag) {
                        $this->or();
                    } else {
                        $this->and();
                    }

                    $this->not($notFlag)->where($column, '=', $value);
                }
            } else {
                throw new InvalidArgumentException(
                    "First parameter must be iterable or object"
                );
            }

            return $this;
        } elseif (func_num_args() == 2) {
            $right = $operator;
            $operator = '=';
        }

        if (is_callable($left)) {
            /** @var Query $this */
            $query = $this->newChild();
            return $this->where($left($query), $operator, $right);
        }

        if (is_callable($right)) {
            /** @var Query $this */
            $query = $this->newChild();
            return $this->where($left, $operator, $right($query));
        }

        $left = Helper::resolveExpression($left, 'left');
        $right = Helper::resolveLiteral($right, 'right');

        $component = new CompareClause;

        $component->setNot($this->getNot());
        $component->setOr($this->getOr());
        $component->setLeft($left);
        $component->setOperator($operator);
        $component->setRight($right);

        /** @var BaseQuery $this */
        return $this->addComponent(ComponentType::Where, $component);
    }

    public function orWhere($left, $operator = null, $right = null): ConditionInterface
    {
        if (func_num_args() == 1) {
            return $this->or()->where($left);
        } elseif (func_num_args() == 2) {
            return $this->or()->where($left, $operator);
        }

        return $this->or()->where($left, $operator, $right);
    }

    public function whereNot($left, $operator = null, $right = null): ConditionInterface
    {
        if (func_num_args() == 1) {
            return $this->not()->where($left);
        } elseif (func_num_args() == 2) {
            return $this->not()->where($left, $operator);
        }

        return $this->not()->where($left, $operator, $right);
    }

    public function orWhereNot($left, $operator = null, $right = null): ConditionInterface
    {
        if (func_num_args() == 1) {
            return $this->or()->not()->where($left);
        } elseif (func_num_args() == 2) {
            return $this->or()->not()->where($left, $operator);
        }

        return $this->or()->not()->where($left, $operator, $right);
    }

    public function whereIn($column, $values): ConditionInterface
    {
        if (is_callable($column)) {
            /** @var Query $this */
            $query = $this->newChild();
            return $this->whereIn($column($query), $values);
        }

        if (is_callable($values)) {
            /** @var Query $this */
            $query = $this->newChild();
            return $this->whereIn($column, $values($query));
        }

        $column = Helper::resolveExpression($column, 'column');

        if (is_iterable($values)) {
            $count = 0;
            if (is_countable($values)) {
                $count = count($values);
            } elseif (is_object($values) && method_exists($values, 'count')) {
                $count = $values->count();
            } else {
                throw new InvalidArgumentException(
                    "Values must countable."
                );
            }

            $resolvedValues = new SplFixedArray($count);
            $index = 0;
            foreach ($values as $value) {
                if (is_scalar($value)) {
                    $resolvedValues[$index] = Expressions::literal($value);
                } elseif ($value instanceof BaseExpression) {
                    $resolvedValues[$index] = $value;
                } elseif (is_object($value) && method_exists($value, '__toString')) {
                    $resolvedValues[$index] = Expressions::literal((string) $value);
                } else {
                    $class = get_class($value);
                    throw new InvalidArgumentException(
                        "Could not resolve '$class' for values item"
                    );
                }

                $index++;
            }

            $values = $resolvedValues;
        } elseif ($values instanceof Query) {
            $values = $values;
        } elseif ($values instanceof BaseExpression) {
            $resolvedValues = new SplFixedArray(1);
            $resolvedValues[0] = $values;
            $values = $resolvedValues;
        } elseif (is_scalar($values) || is_null($values)) {
            $resolvedValues = new SplFixedArray(1);
            $resolvedValues[0] = Expressions::literal($values);
            $values = $resolvedValues;
        } elseif (is_object($values) && method_exists($values, '__toString')) {
            $resolvedValues = new SplFixedArray(1);
            $resolvedValues[0] = Expressions::literal((string) $values);
            $values = $resolvedValues;
        } else {
            $class = get_class($values);
            throw new InvalidArgumentException(
                "Could not resolve '$class' for parameter values"
            );
        }

        $component = new InCondition;

        $component->setOr($this->getOr());
        $component->setNot($this->getNot());
        $component->setColumn($column);
        $component->setValues($values);

        /** @var BaseQuery $this */
        return $this->addComponent(ComponentType::Where, $component);
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
        $resolvedBindings = new SplFixedArray(count($bindings));
        foreach ($bindings as $index => $item) {
            if (is_scalar($item) || is_null($bindings)) {
                $resolvedBindings[$index] = Expressions::literal($item);
            } elseif ($item instanceof BaseExpression) {
                $resolvedBindings[$index] = $item;
            } elseif (is_object($item) && method_exists($item, '__toString')) {
                $resolvedBindings[$index] = Expressions::literal((string) $item);
            } else {
                $class = get_class($item);
                throw new InvalidArgumentException(
                    "Could not resolve '$class' as binding."
                );
            }
        }

        $component = new RawCondition;

        $component->setExpression($expression);
        $component->setBindings($resolvedBindings);

        /** @var static $this */
        return $this->addComponent(ComponentType::Where, $component);
    }

    public function orWhereRaw(string $expression, ...$bindings): ConditionInterface
    {
        return $this->or()->whereRaw($expression, ...$bindings);
    }

    public function whereNull($column): ConditionInterface
    {
        if (is_callable($column)) {
            /** @var Query $this */
            $query = $this->newChild();
            return $this->whereNull($column($query));
        }

        $column = Helper::resolveExpression($column, 'column');

        $component = new NullCondition;

        $component->setOr($this->getOr());
        $component->setNot($this->getNot());
        $component->setColumn($column);

        /** @var BaseQuery $this */
        return $this->addComponent(ComponentType::Where, $component);
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

    public function whereLike($column, $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        if (is_callable($column)) {
            /** @var Query $this */
            $query = $this->newChild();
            return $this->whereLike($column($query), $value, $caseSensitive, $escapeCharacter);
        }

        if (is_callable($value)) {
            /** @var Query $this */
            $query = $this->newChild();
            return $this->whereLike($column, $value($query), $caseSensitive, $escapeCharacter);
        }

        $column = Helper::resolveExpression($column, 'column');
        $value = Helper::resolveLiteral($value, 'value');

        $component = new LikeCondition;

        $component->setNot($this->getNot());
        $component->setOr($this->getOr());
        $component->setLeft($column);
        $component->setType(LikeType::Like);
        $component->setRight($value);
        $component->setEscapeCharacter($escapeCharacter);
        $component->setCaseSensitive($caseSensitive);

        /** @var BaseQuery $this */
        return $this->addComponent(ComponentType::Where, $component);
    }

    public function orWhereLike($column, $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->whereLike($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereNotLike($column, $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->not()->whereLike($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orWhereNotLike($column, $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->not()->whereLike($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereStarts($column, $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        if (is_callable($column)) {
            /** @var Query $this */
            $query = $this->newChild();
            return $this->whereStarts($column($query), $value, $caseSensitive, $escapeCharacter);
        }

        if (is_callable($value)) {
            /** @var Query $this */
            $query = $this->newChild();
            return $this->whereStarts($column, $value($query), $caseSensitive, $escapeCharacter);
        }

        $column = Helper::resolveExpression($column, 'column');
        $value = Helper::resolveLiteral($value, 'value');

        $component = new LikeCondition;

        $component->setNot($this->getNot());
        $component->setOr($this->getOr());
        $component->setLeft($column);
        $component->setType(LikeType::Starts);
        $component->setRight($value);
        $component->setEscapeCharacter($escapeCharacter);
        $component->setCaseSensitive($caseSensitive);

        /** @var BaseQuery $this */
        return $this->addComponent(ComponentType::Where, $component);
    }

    public function orWhereStarts($column, $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->whereStarts($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereNotStarts($column, $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->not()->whereStarts($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orWhereNotStarts($column, $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->not()->whereStarts($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereEnds($column, $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        if (is_callable($column)) {
            /** @var Query $this */
            $query = $this->newChild();
            return $this->whereEnds($column($query), $value, $caseSensitive, $escapeCharacter);
        }

        if (is_callable($value)) {
            /** @var Query $this */
            $query = $this->newChild();
            return $this->whereEnds($column, $value($query), $caseSensitive, $escapeCharacter);
        }

        $column = Helper::resolveExpression($column, 'column');
        $value = Helper::resolveLiteral($value, 'value');

        $component = new LikeCondition;

        $component->setNot($this->getNot());
        $component->setOr($this->getOr());
        $component->setLeft($column);
        $component->setType(LikeType::Ends);
        $component->setRight($value);
        $component->setEscapeCharacter($escapeCharacter);
        $component->setCaseSensitive($caseSensitive);

        /** @var BaseQuery $this */
        return $this->addComponent(ComponentType::Where, $component);
    }

    public function orWhereEnds($column, $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->whereEnds($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereNotEnds($column, $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->not()->whereEnds($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orWhereNotEnds($column, $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->not()->whereEnds($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereContains($column, $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        if (is_callable($column)) {
            /** @var Query $this */
            $query = $this->newChild();
            return $this->whereContains($column($query), $value, $caseSensitive, $escapeCharacter);
        }

        if (is_callable($value)) {
            /** @var Query $this */
            $query = $this->newChild();
            return $this->whereContains($column, $value($query), $caseSensitive, $escapeCharacter);
        }

        $column = Helper::resolveExpression($column, 'column');
        $value = Helper::resolveLiteral($value, 'value');

        $component = new LikeCondition;

        $component->setNot($this->getNot());
        $component->setOr($this->getOr());
        $component->setLeft($column);
        $component->setType(LikeType::Contains);
        $component->setRight($value);
        $component->setEscapeCharacter($escapeCharacter);
        $component->setCaseSensitive($caseSensitive);

        /** @var BaseQuery $this */
        return $this->addComponent(ComponentType::Where, $component);
    }

    public function orWhereContains($column, $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->whereContains($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereNotContains($column, $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->not()->whereContains($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function orWhereNotContains($column, $value, bool $caseSensitive, ?string $escapeCharacter): ConditionInterface
    {
        return $this->or()->not()->whereContains($column, $value, $caseSensitive, $escapeCharacter);
    }

    public function whereBetween($column, $lower, $higher): ConditionInterface
    {
        if (is_callable($column)) {
            /** @var Query $this */
            $query = $this->newChild();
            return $this->whereBetween($column($query), $lower, $higher);
        }

        if (is_callable($lower)) {
            /** @var Query $this */
            $query = $this->newChild();
            return $this->whereBetween($column, $lower($query), $higher);
        }

        if (is_callable($higher)) {
            /** @var Query $this */
            $query = $this->newChild();
            return $this->whereBetween($column, $lower, $higher($query));
        }

        $column = Helper::resolveExpression($column, 'column');
        $lower = Helper::resolveLiteral($lower, 'lower');
        $higher = Helper::resolveLiteral($higher, 'higher');

        $component = new BetweenCondition;
        $component->setNot($this->getNot());
        $component->setOr($this->getOr());
        $component->setColumn($column);
        $component->setLower($lower);
        $component->setHigher($higher);

        /** @var BaseQuery $this */
        return $this->addComponent(ComponentType::Where, $component);
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

    public function whereGroup(callable $group): ConditionInterface
    {
        /** @var Query $this */
        $query = $this->newChild();
        $group = $group($query);

        if (!($group instanceof Query)) {
            $class = get_class($group);
            throw new InvalidArgumentException(
                "Expecting Query, Found '$class' for returning callback of group parameter."
            );
        }

        $component = new GroupCondition;
        $component->setNot($this->getNot());
        $component->setOr($this->getOr());
        $component->setQuery($group);

        /** @var static $this */
        return $this->addComponent(ComponentType::Where, $component);
    }

    public function orWhereGroup(callable $group): ConditionInterface
    {
        return $this->or()->whereGroup($group);
    }

    public function whereExists($query): ConditionInterface
    {
        if (is_callable($query)) {
            /** @var Query $this */
            $child = $this->newChild();
            return $this->whereExists($query($child));
        }

        if (!($query instanceof Query)) {
            $class = get_class($query);
            throw new InvalidArgumentException(
                "Expecting Query, Found '$class' for query parameter."
            );
        }

        return $this;
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
