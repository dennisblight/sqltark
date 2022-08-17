<?php

declare(strict_types=1);

namespace SqlTark;

use SqlTark\Query\Query;

class IncludeQuery
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var Query $query
     */
    protected $query;

    /**
     * @var string $foreignKey
     */
    protected $foreignKey;

    /**
     * @var string $localKey
     */
    protected $localKey;

    /**
     * @var bool $isMany
     */
    protected $isMany;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $value)
    {
        $this->name = $value;
    }

    public function getQuery(): Query
    {
        return $this->query;
    }

    public function setQuery(Query $value)
    {
        $this->query = $value;
    }

    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    public function setForeignKey(string $value)
    {
        $this->foreignKey = $value;
    }

    public function getLocalKey(): string
    {
        return $this->localKey;
    }

    public function setLocalKey(string $value)
    {
        $this->localKey = $value;
    }

    public function getIsMany(): bool
    {
        return $this->isMany;
    }

    public function setIsMany(bool $value)
    {
        $this->isMany = $value;
    }
}
