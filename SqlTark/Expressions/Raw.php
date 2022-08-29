<?php

declare(strict_types=1);

namespace SqlTark\Expressions;

use SplFixedArray;
use SqlTark\Expressions\BaseExpression;

class Raw extends BaseExpression
{
    /**
     * @var ?string
     */
    protected $expression;

    /**
     * @var SplFixedArray<BaseExpression> $bindings
     */
    protected $bindings;

    public function getExpression(): ?string
    {
        return $this->expression;
    }

    public function setExpression(?string $value)
    {
        $this->expression = $value;
    }

    /**
     * @return SplFixedArray<BaseExpression>
     */
    public function getBindings(): SplFixedArray
    {
        return $this->bindings;
    }

    /**
     * @param SplFixedArray<BaseExpression> $value
     */
    public function setBindings(SplFixedArray $value)
    {
        $this->bindings = $value;
    }

    public function __construct(?string $expression, ?iterable $bindings = null)
    {
        $this->expression = $expression;
        $this->bindings = $bindings;
    }
}
