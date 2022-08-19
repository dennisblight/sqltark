<?php

declare(strict_types=1);

namespace SqlTark\Expressions;

class Column extends BaseExpression
{
    /**
     * @var string
     */
    protected $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $value)
    {
        $this->name = $value;
    }

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
