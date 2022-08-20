<?php

declare(strict_types=1);

namespace SqlTark\Query;

use SplObjectStorage;
use InvalidArgumentException;
use SqlTark\Compiler\EngineType;
use SqlTark\Component\AbstractComponent;
use SqlTark\Component\ComponentType;
use SqlTark\Component\FromClause;
use SqlTark\Query\Interfaces\QueryInterface;

/**
 * @method AbstractComponent[] getComponents() Return all components
 * 
 * @method static from(string $table, ?string $alias = null)
 */
abstract class BaseQuery implements QueryInterface
{
    /**
     * @var BaseQuery $parent
     */
    protected $parent = null;

    /**
     * @var SplObjectStorage<AbstractComponent> $components
     */
    protected $components = null;

    /**
     * @var int $engineScope
     */
    protected $engineScope = 0;

    public function getEngineScope(): int
    {
        return $this->engineScope;
    }

    public function setEngineScope(int $value)
    {
        $this->engineScope = $value;
        return $this;
    }

    public function getEngineName(): ?string
    {
        return EngineType::nameOf($this->engineScope);
    }

    public function getParent(): BaseQuery
    {
        return $this->parent;
    }

    public function setParent(BaseQuery $value): BaseQuery
    {
        if ($this == $value) {
            $class = get_class($value);
            throw new InvalidArgumentException("Cannot set the same $class as a parent of itself");
        }

        $this->parent = $value;
        return $this;
    }

    public function newQuery(): Query
    {
        return new Query;
    }

    public function newChild(): Query
    {
        return $this->newQuery()->setParent($this)->setEngineScope($this->engineScope);
    }

    public function addComponent(int $componentType, AbstractComponent $component, int $engineCode = 0): QueryInterface
    {
        $engineCode = $engineCode ?: $this->engineScope;

        if (is_null($this->components)) {
            $this->components = new SplObjectStorage;
        }

        $component->setEngine($engineCode)->setComponentType($componentType);

        $this->components->attach($component);

        return $this;
    }

    public function addOrReplaceComponent(int $componentType, AbstractComponent $component, int $engineCode = 0): QueryInterface
    {
        $engineCode = $engineCode ?: $this->engineScope;

        if (!is_null($this->components)) {
            /** @var ?AbstractComponent */
            $foundComponent = null;
            foreach ($this->components as $value) {
                if ($value->getComponentType() == $componentType && $value->getEngine() == $engineCode) {
                    if (!is_null($foundComponent)) {
                        throw new InvalidArgumentException("Sequence contains more than one matching element");
                    }

                    $foundComponent = $value;
                }
            }

            if (!is_null($foundComponent)) {
                $this->components->detach($foundComponent);
            }
        } else {
            $this->components = new SplObjectStorage;
        }

        $component->setEngine($engineCode)->setComponentType($componentType);
        $this->components->attach($component);

        return $this;
    }

    /**
     * @return AbstractComponent[]
     */
    public function getComponents(int $componentType = 0, int $engineCode = 0): array
    {
        if (is_null($this->components)) {
            return [];
        }

        if (func_num_args() == 0) {
            return iterator_to_array($this->components);
        }

        $engineCode = $engineCode ?: $this->engineScope;

        $result = [];
        foreach ($this->components as $item) {
            if (self::isValidComponent($item, $componentType, $engineCode)) {
                $result[] = $item;
            }
        }

        return $result;
    }

    public function getOneComponent(int $componentType, int $engineCode = 0): ?AbstractComponent
    {
        if (is_null($this->components)) return null;

        $engineCode = $engineCode ?: $this->engineScope;

        /** @var ?AbstractComponent */
        $anyComponent = null;
        foreach ($this->components as $item) {
            if ($item->getComponentType() != $componentType) {
                continue;
            }

            if ($item->getEngine() == $engineCode) {
                return $item;
            }

            if ($item->getEngine() == 0 && $engineCode == 0) {
                $anyComponent = $item;
            }
        }

        return $anyComponent;
    }

    public function hasComponent(int $componentType, int $engineCode = 0): bool
    {
        if (is_null($this->components)) return false;

        $engineCode = $engineCode ?: $this->engineScope;

        foreach ($this->components as $item) {
            if (self::isValidComponent($item, $componentType, $engineCode)) {
                return true;
            }
        }

        return false;
    }

    private static function isValidComponent(AbstractComponent $component, int $componentType, int $engineCode): bool
    {
        return $component->getComponentType() == $componentType
            && ($component->getEngine() == 0
                || $engineCode == 0
                || $engineCode == $component->getEngine()
            );
    }

    public function __clone()
    {
        return $this->clone();
    }

    public function clone(): BaseQuery
    {
        $self = new static;

        if (!is_null($this->components)) {
            $self->components = new SplObjectStorage;
            foreach ($this->components as $item) {
                $self->components->attach(clone $item);
            }
        }

        return $self;
    }

    public function for(int $engine, callable $callback): QueryInterface
    {
        $this->engineScope = $engine;

        $result = $callback($this);

        $this->engineScope = 0;

        return $result;
    }

    public function when(bool $condition, ?callable $whenTrue, ?callable $whenFalse): QueryInterface
    {
        if($condition && !is_null($whenTrue))
        {
            return $whenTrue($this);
        }
        elseif(!$condition && !is_null($whenFalse))
        {
            return $whenFalse($this);
        }

        return $this;
    }

    public function from($table, ?string $alias = null): QueryInterface
    {
        $component = null;
        if (is_string($table)) {
            $component = new FromClause;

            $component->setTable($table);
            $component->setAlias($alias);
        } elseif (method_exists($table, '__toString')) {
            return $this->from((string) $table, $alias);
        } elseif (is_object($table)) {
            return $this->from(get_class($table), $alias);
        } else {
            $str = get_class($table);
            throw new InvalidArgumentException("Could not resolve '$str' as table");
        }

        return $this->addOrReplaceComponent(ComponentType::From, $component);
    }
}
