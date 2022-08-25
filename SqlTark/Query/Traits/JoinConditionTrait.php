<?php

declare(strict_types=1);

namespace SqlTark\Query\Traits;

use SqlTark\Helper;
use InvalidArgumentException;
use SqlTark\Component\CompareClause;
use SqlTark\Component\ComponentType;

trait JoinConditionTrait
{
    use ConditionTrait;

    /**
     * On compare two value using ```and``` clause
     * @return $this Self object
     */
    public function on($left, $operator = null, $right = null)
    {
        if (func_num_args() == 1) {
            if (is_iterable($left)) {
                $orFlag = $this->getOr();
                $notFlag = $this->getNot();
                foreach ($left as $column => $value) {
                    if ($orFlag) {
                        $this->or();
                    } else {
                        $this->and();
                    }
                    $this->not($notFlag)->withWhere()->condition($column, '=', $value);
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
        $right = Helper::resolveExpression($right, 'right');

        $component = new CompareClause;

        $component->setNot($this->getNot());
        $component->setOr($this->getOr());
        $component->setLeft($left);
        $component->setOperator($operator);
        $component->setRight($right);

        /** @var BaseQuery $this */
        return $this->addComponent(ComponentType::Where, $component);
    }

    /**
     * On compare two value using ```or``` clause
     * @return $this Self object
     */
    public function orOn($left, $operator = null, $right = null)
    {
        return call_user_func_array([$this->or(), 'on'], func_get_args());
    }

    /**
     * On compare two value using ```not``` clause
     * @return $this Self object
     */
    public function onNot($left, $operator = null, $right = null)
    {
        return call_user_func_array([$this->not(), 'on'], func_get_args());
    }

    /**
     * On compare two value ```or``` and ```not``` clause
     * @return $this Self object
     */
    public function orOnNot($left, $operator = null, $right = null)
    {
        return call_user_func_array([$this->or()->not(), 'on'], func_get_args());
    }
}
