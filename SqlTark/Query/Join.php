<?php

declare(strict_types=1);

namespace SqlTark\Query;

use SqlTark\Component\JoinType;
use SqlTark\Query\BaseQuery;
use SqlTark\Query\Traits\FromTrait;
use SqlTark\Query\Interfaces\ConditionInterface;
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
     * @return static Self object
     */
    public function joinWith($table, ?string $alias = null)
    {
        return $this->from($table, $alias);
    }

    /**
     * @return static Self object
     */
    public function asType(int $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return static Self object
     */
    public function asInnerJoin()
    {
        return $this->asType(JoinType::InnerJoin);
    }

    /**
     * @return static Self object
     */
    public function asLeftJoin()
    {
        return $this->asType(JoinType::LeftJoin);
    }

    /**
     * @return static Self object
     */
    public function asRightJoin()
    {
        return $this->asType(JoinType::RightJoin);
    }

    /**
     * @return static Self object
     */
    public function asOuterJoin()
    {
        return $this->asType(JoinType::OuterJoin);
    }

    /**
     * @return static Self object
     */
    public function asNaturalJoin()
    {
        return $this->asType(JoinType::NaturalJoin);
    }

    /**
     * @return static Self object
     */
    public function asLeftOuterJoin()
    {
        return $this->asType(JoinType::LeftOuterJoin);
    }

    /**
     * @return static Self object
     */
    public function asRightOuterJoin()
    {
        return $this->asType(JoinType::RightOuterJoin);
    }

    /**
     * @return static Self object
     */
    public function asFullOuterJoin()
    {
        return $this->asType(JoinType::FullOuterJoin);
    }
}
