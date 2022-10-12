<?php

declare(strict_types=1);

namespace SqlTark\Query;

use SplObjectStorage;
use InvalidArgumentException;
use SqlTark\Component\AbstractComponent;
use SqlTark\Query\Interfaces\QueryInterface;

/**
 * @method AbstractComponent[] getComponents() Return all components
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
     * @var int $method
     */
    protected $method = MethodType::Select;

    public function getMethod(): int
    {
        return $this->method;
    }

    public function getMethodName(): ?string
    {
        return MethodType::nameOf($this->method);
    }

    public function setMethod(int $value)
    {
        $this->method = $value;
    }

    public function getParent(): BaseQuery
    {
        return $this->parent;
    }

    public function setParent(BaseQuery $value): BaseQuery
    {
        if ($this === $value) {
            $class = get_class($value);
            throw new InvalidArgumentException("Cannot set the same $class as a parent of itself");
        }

        $this->parent = $value;
        return $this;
    }

    /**
     * @return Query
     */
    public function newQuery()
    {
        return new Query;
    }

    /**
     * @return Query
     */
    public function newChild()
    {
        return $this->newQuery()->setParent($this);
    }

    public function addComponent(int $componentType, AbstractComponent $component): QueryInterface
    {
        if (is_null($this->components)) {
            $this->components = new SplObjectStorage;
        }

        $component->setComponentType($componentType);

        $this->components->attach($component);

        return $this;
    }

    public function addOrReplaceComponent(int $componentType, AbstractComponent $component): QueryInterface
    {
        if (!is_null($this->components)) {
            /** @var ?AbstractComponent */
            $foundComponent = null;
            foreach ($this->components as $value) {
                if ($value->getComponentType() == $componentType) {
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

        $component->setComponentType($componentType);
        $this->components->attach($component);

        return $this;
    }

    /**
     * @return $this Self object
     */
    public function clearComponents(int $componentType)
    {
        if(!is_null($this->components))
        {
            foreach ($this->components as $value) {
                if ($value->getComponentType() == $componentType) {
                    $this->components->detach($value);
                }
            }
        }

        return $this;
    }

    /**
     * @return AbstractComponent[]
     */
    public function getComponents(int $componentType = 0): array
    {
        if (is_null($this->components)) {
            return [];
        }

        if (func_num_args() == 0) {
            return iterator_to_array($this->components);
        }

        $result = [];
        foreach ($this->components as $item) {
            if (self::isValidComponent($item, $componentType)) {
                $result[] = $item;
            }
        }

        return $result;
    }

    public function getOneComponent(int $componentType): ?AbstractComponent
    {
        if (is_null($this->components)) return null;

        /** @var ?AbstractComponent */
        $anyComponent = null;
        foreach ($this->components as $item) {
            if ($item->getComponentType() == $componentType) {
                return $item;
            }
        }

        return $anyComponent;
    }

    public function hasComponent(int $componentType): bool
    {
        if (is_null($this->components)) return false;

        foreach ($this->components as $item) {
            if (self::isValidComponent($item, $componentType)) {
                return true;
            }
        }

        return false;
    }

    private static function isValidComponent(AbstractComponent $component, int $componentType): bool
    {
        return $component->getComponentType() == $componentType;
    }

    public function __clone()
    {
        return $this->clone();
    }

    /**
     * @return static Clone of current object
     */
    public function clone()
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

    /**
     * @return $this Self object
     */
    public function when(bool $condition, ?callable $whenTrue, ?callable $whenFalse)
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
}
