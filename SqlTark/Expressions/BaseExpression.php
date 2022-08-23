<?php

declare(strict_types=1);

namespace SqlTark\Expressions;

abstract class BaseExpression
{
    /**
     * @var ?string $wrap
     */
    protected $wrap;

    public function getWrap(): ?string
    {
        return $this->wrap;
    }

    /**
     * @return $this Self object
     */
    public function wrap(?string $value): BaseExpression
    {
        $this->wrap = $value;
        return $this;
    }
}
