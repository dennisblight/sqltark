<?php

declare(strict_types=1);

namespace SqlTark\Query\Traits;

use InvalidArgumentException;
use SqlTark\Component\ComponentType;
use SqlTark\Component\JoinClause;
use SqlTark\Component\JoinType;
use SqlTark\Helper;
use SqlTark\Query\Condition;
use SqlTark\Query\Join;

trait JoinTrait
{
    private $joinType = JoinType::Join;

    /**
     * @return $this Self object
     */
    public function join($table, $left = null, $operator = null, $right = null, $joinType = JoinType::Join)
    {
        if (func_num_args() == 1) {
            $table = Helper::resolveJoin($table, $this);
            if (!($table instanceof Join)) {
                $class = Helper::getType($table);
                throw new InvalidArgumentException(
                    "Could not resolve '$class' for join parameter"
                );
            }

            if (!$table->hasComponent(ComponentType::From)) {
                throw new InvalidArgumentException(
                    "Join must have from/join table"
                );
            }

            if ($this->joinType) {
                $table->asType($this->joinType);
            }

            $component = new JoinClause;
            $component->setJoin($table);

            return $this->addComponent(ComponentType::Join, $component);
        }

        if (func_num_args() == 2) {
            if (is_string($left)) {
                $join = new Join($table, $this->joinType);
                $join->whereRaw($left);
            } elseif (is_callable($left)) {
                $join = new Join($table, $this->joinType);
                $join->setParent($this);

                $join = $left($join);

                if (!($join instanceof Join)) {
                    $class = Helper::getType($join);
                    throw new InvalidArgumentException(
                        "Invalid return from callback. Expected 'Join' found '$class'"
                    );
                }
            } elseif ($left instanceof Condition) {
                $join = new Join($table, $this->joinType);
                $join->whereGroup($left);
            } else {
                $class = Helper::getType($left);
                throw new InvalidArgumentException(
                    "Could not resolve '$class' for join parameter"
                );
            }

            $component = new JoinClause;
            $component->setJoin($join);

            return $this->addComponent(ComponentType::Join, $component);
        }

        if (func_num_args() == 3) {
            $right = $operator;
            $operator = '=';
        }

        $join = new Join($table, $this->joinType ?: $joinType);
        $join->on($left, $operator, $right);
        $component = new JoinClause;
        $component->setJoin($join);

        return $this->addComponent(ComponentType::Join, $component);
    }

    /**
     * @return $this Self object
     */
    public function innerJoin($table, $left = null, $operator = null, $right = null)
    {
        $this->joinType = JoinType::InnerJoin;
        $result = call_user_func_array([$this, 'join'], func_get_args());
        $this->joinType = JoinType::Join;
        return $result;
    }

    /**
     * @return $this Self object
     */
    public function leftJoin($table, $left = null, $operator = null, $right = null)
    {
        $this->joinType = JoinType::LeftJoin;
        $result = call_user_func_array([$this, 'join'], func_get_args());
        $this->joinType = JoinType::Join;
        return $result;
    }

    /**
     * @return $this Self object
     */
    public function rightJoin($table, $left = null, $operator = null, $right = null)
    {
        $this->joinType = JoinType::RightJoin;
        $result = call_user_func_array([$this, 'join'], func_get_args());
        $this->joinType = JoinType::Join;
        return $result;
    }

    /**
     * @return $this Self object
     */
    public function leftOuterJoin($table, $left = null, $operator = null, $right = null)
    {
        $this->joinType = JoinType::LeftOuterJoin;
        $result = call_user_func_array([$this, 'join'], func_get_args());
        $this->joinType = JoinType::Join;
        return $result;
    }

    /**
     * @return $this Self object
     */
    public function rightOuterJoin($table, $left = null, $operator = null, $right = null)
    {
        $this->joinType = JoinType::RightOuterJoin;
        $result = call_user_func_array([$this, 'join'], func_get_args());
        $this->joinType = JoinType::Join;
        return $result;
    }

    /**
     * @return $this Self object
     */
    public function outerJoin($table, $left = null, $operator = null, $right = null)
    {
        $this->joinType = JoinType::OuterJoin;
        $result = call_user_func_array([$this, 'join'], func_get_args());
        $this->joinType = JoinType::Join;
        return $result;
    }

    /**
     * @return $this Self object
     */
    public function fullOuterJoin($table, $left = null, $operator = null, $right = null)
    {
        $this->joinType = JoinType::FullOuterJoin;
        $result = call_user_func_array([$this, 'join'], func_get_args());
        $this->joinType = JoinType::Join;
        return $result;
    }

    /**
     * @return $this Self object
     */
    public function naturalJoin($table)
    {
        $join = new Join($table, JoinType::NaturalJoin);
        $component = new JoinClause;
        $component->setJoin($join);

        return $this->addComponent(ComponentType::Join, $join);
    }

    /**
     * @return $this Self object
     */
    public function crossJoin($table)
    {
        $join = new Join($table, JoinType::CrossJoin);
        $component = new JoinClause;
        $component->setJoin($join);

        return $this->addComponent(ComponentType::Join, $join);
    }
}
