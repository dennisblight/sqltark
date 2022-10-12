<?php

declare(strict_types=1);

namespace SqlTark\Query\Interfaces;

use SqlTark\Query\BaseQuery;
use SqlTark\Component\AbstractComponent;

interface QueryInterface
{
    /**
     * Get query parent
     * @return ?BaseQuery Parent
     */
    public function getParent(): ?BaseQuery;

    /**
     * Set query parent
     * @param BaseQuery $value Parent
     */
    public function setParent(BaseQuery $value): BaseQuery;

    /**
     * Create new ```Query``` object
     */
    public function newQuery();

    /**
     * Create new ```Query``` object then set parent as ```this``` object
     */
    public function newChild();

    /**
     * Add new component
     * 
     * @param int $componentType Component type from ```ComponentType``` enum class
     * @param AbstractComponent $component Component object
     * @return $this Self object
     */
    public function addComponent(int $componentType, AbstractComponent $component): QueryInterface;

    /**
     * Remove old component with specified ```componentType``` if exists then
     * add new component.
     * 
     * @param int $componentType Component type from ```ComponentType``` enum class
     * @param AbstractComponent $component Component object
     * @return $this Self object
     */
    public function addOrReplaceComponent(int $componentType, AbstractComponent $component): QueryInterface;

    /**
     * Get all components with specified ```componentType```.
     * 
     * Get all components when no parameter is specified.
     * 
     * @param int $componentType Component type from ```ComponentType``` enum class
     * @return AbstractComponent[] Components.
     */
    public function getComponents(int $componentType = 0): array;

    /**
     * Get single components with specified ```componentType```.
     * 
     * @param int $componentType Component type from ```ComponentType``` enum class
     * @return ?AbstractComponent Component.
     */
    public function getOneComponent(int $componentType): ?AbstractComponent;

    /**
     * Check wether components with specified ```componentType``` is exists.
     * 
     * @param int $componentType Component type from ```ComponentType``` enum class
     * @return bool Is exists.
     */
    public function hasComponent(int $componentType): bool;

    /**
     * Execute query when condition is fulfilled
     * 
     * @param bool $condition Condition
     * @param callable|null $whenTrue Query when true
     * @param callable|null $whenFalse Query when false
     * @return $this Self object
     */
    public function when(bool $condition, ?callable $whenTrue, ?callable $whenFalse);
}
