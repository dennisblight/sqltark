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
use SqlTark\Component\ExistsCondition;
use SqlTark\Expressions\BaseExpression;
use SqlTark\Query\Condition;
use SqlTark\Query\Interfaces\ConditionInterface;

trait BaseConditionTrait
{
    /**
     * @var bool $orFlag
     */
    protected $orFlag = false;

    /**
     * @var bool $notFlag
     */
    protected $notFlag = false;

    /**
     * @var bool $havingFlag
     */
    protected $havingFlag = false;

    /**
     * @return static Self object
     */
    protected function and(): ConditionInterface
    {
        $this->orFlag = false;
        return $this;
    }

    /**
     * @return static Self object
     */
    public function or(): ConditionInterface
    {
        $this->orFlag = true;
        return $this;
    }

    /**
     * @return static Self object
     */
    public function not(bool $value = true): ConditionInterface
    {
        $this->notFlag = $value;
        return $this;
    }

    protected function withHaving()
    {
        $this->havingFlag = true;
        return $this;
    }

    protected function withWhere()
    {
        $this->havingFlag = false;
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

    protected function getHaving(): bool
    {
        $return = $this->havingFlag;

        $this->havingFlag = false;

        return $return;
    }

    protected function condition($left, $operator = null, $right = null): ConditionInterface
    {
        if (func_num_args() == 1) {
            if (is_iterable($left)) {
                $orFlag = $this->getOr();
                $notFlag = $this->getNot();
                $havingFlag = $this->getHaving();
                foreach ($left as $column => $value) {
                    if ($orFlag) {
                        $this->or();
                    } else {
                        $this->and();
                    }
                    if ($havingFlag) {
                        $this->withHaving();
                    } else {
                        $this->withWhere();
                    }

                    $this->not($notFlag)->condition($column, '=', $value);
                }
            } else {
                throw new InvalidArgumentException(
                    "First parameter must be iterable"
                );
            }

            return $this;
        } elseif (func_num_args() == 2) {
            $right = $operator;
            $operator = '=';
        }

        $left = Helper::resolveQuery($left, $this);
        $right = Helper::resolveQuery($right, $this);

        $left = Helper::resolveExpression($left, 'left');
        $right = Helper::resolveLiteral($right, 'right');

        $component = new CompareClause;

        $component->setNot($this->getNot());
        $component->setOr($this->getOr());
        $component->setLeft($left);
        $component->setOperator($operator);
        $component->setRight($right);
        $componentType = $this->getHaving() ? ComponentType::Having : ComponentType::Where;

        /** @var BaseQuery $this */
        return $this->addComponent($componentType, $component);
    }

    protected function conditionIn($column, $values): ConditionInterface
    {
        $column = Helper::resolveQuery($column, $this);
        $values = Helper::resolveQuery($values, $this);

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
                $value = Helper::resolveLiteral($value, 'value');
                if (!($value instanceof BaseExpression)) {
                    $class = Helper::getType($value);
                    throw new InvalidArgumentException(
                        "Could not resolve '$class' for values item"
                    );
                }

                $index++;
            }

            $values = $resolvedValues;
        } else {
            $values = Helper::resolveLiteral($values, 'values');
            if ($values instanceof BaseExpression) {
                $resolvedValues = new SplFixedArray(1);
                $resolvedValues[0] = $values;
                $values = $resolvedValues;
            }
        }

        $component = new InCondition;

        $component->setOr($this->getOr());
        $component->setNot($this->getNot());
        $component->setColumn($column);
        $component->setValues($values);
        $componentType = $this->getHaving() ? ComponentType::Having : ComponentType::Where;

        /** @var static $this */
        return $this->addComponent($componentType, $component);
    }

    protected function conditionNull($column): ConditionInterface
    {
        $column = Helper::resolveQuery($column, $this);
        $column = Helper::resolveExpression($column, 'column');

        $component = new NullCondition;

        $component->setOr($this->getOr());
        $component->setNot($this->getNot());
        $component->setColumn($column);
        $componentType = $this->getHaving() ? ComponentType::Having : ComponentType::Where;

        /** @var static $this */
        return $this->addComponent($componentType, $component);
    }

    protected function conditionLike($column, $value, bool $caseSensitive, ?string $escapeCharacter, $type = LikeType::Like): ConditionInterface
    {
        $column = Helper::resolveQuery($column, $this);
        $value = Helper::resolveQuery($value, $this);

        $column = Helper::resolveExpression($column, 'column');
        $value = Helper::resolveLiteral($value, 'value');

        $component = new LikeCondition;

        $component->setNot($this->getNot());
        $component->setOr($this->getOr());
        $component->setLeft($column);
        $component->setType($type);
        $component->setRight($value);
        $component->setEscapeCharacter($escapeCharacter);
        $component->setCaseSensitive($caseSensitive);
        $componentType = $this->getHaving() ? ComponentType::Having : ComponentType::Where;

        /** @var static $this */
        return $this->addComponent($componentType, $component);
    }

    protected function conditionBetween($column, $lower, $higher): ConditionInterface
    {
        $column = Helper::resolveQuery($column, $this);
        $lower = Helper::resolveQuery($lower, $this);
        $higher = Helper::resolveQuery($higher, $this);

        $column = Helper::resolveExpression($column, 'column');
        $lower = Helper::resolveLiteral($lower, 'lower');
        $higher = Helper::resolveLiteral($higher, 'higher');

        $component = new BetweenCondition;
        $component->setNot($this->getNot());
        $component->setOr($this->getOr());
        $component->setColumn($column);
        $component->setLower($lower);
        $component->setHigher($higher);
        $componentType = $this->getHaving() ? ComponentType::Having : ComponentType::Where;

        /** @var static $this */
        return $this->addComponent($componentType, $component);
    }

    protected function conditionGroup($group): ConditionInterface
    {
        if (is_callable($group)) {
            $query = new Condition;
            $query->setParent($this)->setEngineScope($this->getEngineScope());
            $group = $group($query);

            if (!($group instanceof Condition)) {
                $class = Helper::getType($group);
                throw new InvalidArgumentException(
                    "Invalid return from callback. Expected 'Condition' found '$class'"
                );
            }
        }

        if (!($group instanceof Condition)) {
            $class = Helper::getType($group);
            throw new InvalidArgumentException(
                "Expecting Condition, Found '$class' for returning callback of group parameter."
            );
        }

        $component = new GroupCondition;
        $component->setNot($this->getNot());
        $component->setOr($this->getOr());
        $component->setCondition($group);
        $componentType = $this->getHaving() ? ComponentType::Having : ComponentType::Where;

        /** @var static $this */
        return $this->addComponent($componentType, $component);
    }

    protected function conditionExists($query): ConditionInterface
    {
        $query = Helper::resolveQuery($query, $this);
        if (!($query instanceof Query)) {
            $class = Helper::getType($query);
            throw new InvalidArgumentException(
                "Expecting Query, Found '$class' for query parameter."
            );
        }

        $component = new ExistsCondition;
        $component->setNot($this->getNot());
        $component->setOr($this->getOr());
        $component->setQuery($query);
        $componentType = $this->getHaving() ? ComponentType::Having : ComponentType::Where;

        /** @var static $this */
        return $this->addComponent($componentType, $component);
    }

    public function conditionRaw(string $expression, ...$bindings): ConditionInterface
    {
        $resolvedBindings = new SplFixedArray(count($bindings));
        foreach ($bindings as $index => $item) {
            if (is_scalar($item) || is_null($item) || $item instanceof \DateTime) {
                $resolvedBindings[$index] = Expressions::literal($item);
            } elseif ($item instanceof BaseExpression) {
                $resolvedBindings[$index] = $item;
            } else {
                $class = Helper::getType($item);
                throw new InvalidArgumentException(
                    "Could not resolve '$class' as binding."
                );
            }
        }

        $component = new RawCondition;

        $component->setExpression($expression);
        $component->setBindings($resolvedBindings);
        $componentType = $this->getHaving() ? ComponentType::Having : ComponentType::Where;

        /** @var static $this */
        return $this->addComponent($componentType, $component);
    }
}
