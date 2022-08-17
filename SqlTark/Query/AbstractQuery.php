<?php

declare(strict_types=1);

namespace SqlTark\Query;

use InvalidArgumentException;

abstract class AbstractQuery
{
    /**
     * @var AbstractQuery
     */
    protected $parent;

    public function getParent(): AbstractQuery
    {
        return $this->parent;
    }

    public function setParent(AbstractQuery $value): AbstractQuery
    {
        if ($this == $value) {
            throw new InvalidArgumentException("Cannot set the same AbstractQuery as a parent of itself");
        }

        $this->parent = $value;
        return $this;
    }
}
