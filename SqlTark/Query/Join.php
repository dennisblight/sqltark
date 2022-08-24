<?php

declare(strict_types=1);

namespace SqlTark\Query;

use SqlTark\Component\JoinType;
use SqlTark\Query\BaseQuery;
use SqlTark\Query\Traits\FromTrait;
use SqlTark\Query\Interfaces\ConditionInterface;
use SqlTark\Query\Interfaces\QueryInterface;
use SqlTark\Query\Traits\JoinConditionTrait;

/**
 * @method static joinWith(string $table, ?string $alias = null)
 * 
 * @method static joinWith(Query $query, ?string $alias = null)
 * 
 * @method static joinWith(callable $callback, ?string $alias = null)
 */
class Join extends BaseQuery implements ConditionInterface
{
    use FromTrait, JoinConditionTrait;

    /**
     * @var int $type
     */
    protected $type = JoinType::Join;

    public function getType(): int
    {
        return $this->type;
    }

    public function getTypeName(): ?string
    {
        return JoinType::nameOf($this->type);
    }

    public function __construct($table = null, $type = JoinType::Join)
    {
        if (!is_null($table)) {
            $this->from($table);
        }

        $this->asType($type);
    }

    /**
     * @return $this Self object
     */
    public function joinWith($table, ?string $alias = null)
    {
        return $this->from($table, $alias);
    }

    /**
     * @return $this Self object
     */
    public function asType(int $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return $this Self object
     */
    public function asInnerJoin()
    {
        return $this->asType(JoinType::InnerJoin);
    }

    /**
     * @return $this Self object
     */
    public function asLeftJoin()
    {
        return $this->asType(JoinType::LeftJoin);
    }

    /**
     * @return $this Self object
     */
    public function asRightJoin()
    {
        return $this->asType(JoinType::RightJoin);
    }

    /**
     * @return $this Self object
     */
    public function asOuterJoin()
    {
        return $this->asType(JoinType::OuterJoin);
    }

    /**
     * @return $this Self object
     */
    public function asNaturalJoin()
    {
        return $this->asType(JoinType::NaturalJoin);
    }

    /**
     * @return $this Self object
     */
    public function asLeftOuterJoin()
    {
        return $this->asType(JoinType::LeftOuterJoin);
    }

    /**
     * @return $this Self object
     */
    public function asRightOuterJoin()
    {
        return $this->asType(JoinType::RightOuterJoin);
    }

    /**
     * @return $this Self object
     */
    public function asFullOuterJoin()
    {
        return $this->asType(JoinType::FullOuterJoin);
    }

    /**
     * @return static Clone of current object
     */
    public function clone()
    {
        $self = parent::clone();

        $self->alias = $this->alias;
        $self->orFlag = $this->orFlag;
        $self->notFlag = $this->notFlag;
        $self->type = $this->type;

        return $self;
    }
}
