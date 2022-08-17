<?php

declare(strict_types=1);

namespace SqlTark\Query;

use InvalidArgumentException;
use SqlTark\Clauses\AbstractClause;
use SqlTark\Clauses\From\RawFromClause;
use SqlTark\Clauses\From\QueryFromClause;

abstract class BaseQuery extends AbstractQuery
{
    /**
     * @var AbstractClause[] $clauses
     */
    protected $clauses = [];

    /**
     * @var bool $orFlag
     */
    private $orFlag = false;

    /**
     * @var bool $notFlag
     */
    private $notFlag = false;

    /**
     * @var string $engineScope
     */
    protected $engineScope;

    /**
     * @return AbstractClause[]
     */
    public function getClauses(): array
    {
        return $this->clauses;
    }

    /**
     * @param AbstractClause[] $value
     */
    public function setClauses(array $value): BaseQuery
    {
        $this->clauses = $value;
        return $this;
    }

    public function getEngineScope(): string
    {
        return $this->engineScope;
    }

    public function setEngineScope(string $value): BaseQuery
    {
        $this->engineScope = $value;
        return $this;
    }

    public function __construct()
    {
    }

    public function clone(): BaseQuery
    {
        $query = $this->newQuery();

        /**
         * @param AbstractClause $item
         */
        $query->clauses = array_map(function ($item) {
            return clone $item;
        }, $this->clauses);

        return $query;
    }

    public function __clone()
    {
        return $this->clone();
    }

    /**
     * @return BaseQuery
     */
    public function setParent(AbstractQuery $value): AbstractQuery
    {
        return parent::setParent($value);
    }

    public abstract function newQuery(): BaseQuery;

    public function newChild(): BaseQuery
    {
        return $this->newQuery()->setParent($this)->setEngineScope($this->engineScope);
    }

    public function addComponent(string $component, AbstractClause $clause, string $engineCode = null): BaseQuery
    {
        $this->clauses[] = $clause
            ->setEngine($engineCode ?? $this->engineScope)
            ->setComponent($component);

        return $this;
    }

    public function addOrReplaceComponent(string $component, AbstractClause $clause, string $engineCode = null): BaseQuery
    {
        $engineCode = $engineCode ?? $this->engineScope;

        $clause->setEngine($engineCode)->setComponent($component);

        $found = false;
        foreach ($this->clauses as $key => $value) {
            if ($value->getComponent() == $component && $value->getEngine() == $engineCode) {
                if ($found) {
                    throw new InvalidArgumentException("Sequence contains more than one matching element");
                }

                $this->clauses[$key] = $clause;
                $this->clauses = array_values($this->clauses);
                $found = true;
            }
        }

        if (!$found) {
            $this->clauses[] = $clause;
        }

        return $this;
    }

    /**
     * @return AbstractClause[]
     */
    public function getComponents(string $component, string $engineCode = null): array
    {
        $engineCode = $engineCode ?? $this->engineScope;

        /**
         * @param AbstractClause $item
         */
        return array_filter($this->clauses, function ($item) use ($component, $engineCode) {
            return self::isValidComponent($item, $component, $engineCode);
        });
    }

    public function getOneComponent(string $component, string $engineCode = null): AbstractClause
    {
        $engineCode = $engineCode ?? $this->engineScope;

        $anyComponent = null;

        foreach ($this->clauses as $item) {
            if ($item->getComponent() != $component) {
                continue;
            }

            if ($item->getEngine() == $engineCode) {
                return $item;
            }

            if (is_null($item->getEngine()) && is_null($anyComponent)) {
                $anyComponent = $item;
            }
        }

        return $anyComponent;
    }

    public function hasComponent(string $component, string $engineCode = null): bool
    {
        $engineCode = $engineCode ?? $this->engineScope;

        foreach ($this->clauses as $item) {
            if (self::isValidComponent($item, $component, $engineCode)) {
                return true;
            }
        }

        return false;
    }

    public function clearComponent(string $component, string $engineCode = null): BaseQuery
    {
        $engineCode = $engineCode ?? $this->engineScope;
        $newClauses = [];
        foreach ($this->clauses as $item) {
            if ($this->isValidComponent($item, $component, $engineCode)) {
                $newClauses[] = $item;
            }
        }
        $this->clauses = $newClauses;
        return $this;
    }

    private static function isValidComponent(AbstractClause $clause, string $component, string $engineCode): bool
    {
        return $clause->getComponent() == $component && (is_null($clause->getEngine()) || is_null($engineCode) || $clause->getEngine() == $engineCode);
    }

    protected function and(): BaseQuery
    {
        $this->orFlag = false;
        return $this;
    }

    protected function or(): BaseQuery
    {
        $this->orFlag = true;
        return $this;
    }

    protected function not(bool $value = true): BaseQuery
    {
        $this->notFlag = $value;
        return $this;
    }

    protected function getOr(): bool
    {
        $result = $this->orFlag;

        $this->orFlag = false;

        return $result;
    }

    protected function getNot(): bool
    {
        $result = $this->notFlag;

        $this->notFlag = false;

        return $result;
    }

    public function from(Query $query, string $alias = null): BaseQuery
    {
        $query = clone $query;
        $query->setParent($this);

        if (!is_null($alias)) {
            $query->as($alias);
        }

        $clause = new QueryFromClause;
        $clause->setQuery($query);

        return $this->addOrReplaceComponent('from', $clause);
    }

    public function fromRaw(string $sql, ...$bindings): BaseQuery
    {
        $clause = new RawFromClause;
        $clause->setExpression($sql);
        $clause->setBindings($bindings);

        return $this->addOrReplaceComponent('from', $clause);
    }
}
