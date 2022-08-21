<?php

declare(strict_types=1);

namespace SqlTark\Query\Interfaces;

use SqlTark\Query\Query;
use SqlTark\Query\BaseQuery;
use SqlTark\Component\AbstractComponent;
use SqlTark\Component\ComponentType;

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
     * @return Query New query
     */
    public function newQuery(): Query;

    /**
     * Create new ```Query``` object then set parent as ```this``` object
     * @return Query New query
     */
    public function newChild(): Query;

    /**
     * Add new component
     * 
     * @param int $componentType Component type from ```ComponentType``` enum class
     * @param AbstractComponent $component Component object
     * @param int $engineCode Engine type from ```EngineType``` enum class
     * @return static Self object
     */
    public function addComponent(int $componentType, AbstractComponent $component, int $engineCode = 0): QueryInterface;

    /**
     * Remove old component with specified ```componentType``` if exists then
     * add new component.
     * 
     * @param int $componentType Component type from ```ComponentType``` enum class
     * @param AbstractComponent $component Component object
     * @param int $engineCode Engine type from ```EngineType``` enum class
     * @return static Self object
     */
    public function addOrReplaceComponent(int $componentType, AbstractComponent $component, int $engineCode = 0): QueryInterface;

    /**
     * Get all components with specified ```componentType``` and ```engineCode```.
     * Component with no ```engineCode``` (0) also will be added.
     * 
     * Get all components when no parameter is specified.
     * 
     * @param int $componentType Component type from ```ComponentType``` enum class
     * @param int $engineCode Engine type from ```EngineType``` enum class
     * @return AbstractComponent[] Components.
     */
    public function getComponents(int $componentType = 0, int $engineCode = 0): array;

    /**
     * Get single components with specified ```componentType``` and ```engineCode```.
     * Any component with specified ```componentType``` will be returned if component
     * with specified ```engineCode``` was not found.
     * 
     * @param int $componentType Component type from ```ComponentType``` enum class
     * @param int $engineCode Engine type from ```EngineType``` enum class
     * @return ?AbstractComponent Component.
     */
    public function getOneComponent(int $componentType, int $engineCode = 0): ?AbstractComponent;

    /**
     * Check wether components with specified ```componentType``` and ```engineCode``` is exists.
     * 
     * @param int $componentType Component type from ```ComponentType``` enum class
     * @param int $engineCode Engine type from ```EngineType``` enum class
     * @return bool Is exists.
     */
    public function hasComponent(int $componentType, int $engineCode = 0): bool;

    /**
     * @param int $engine Engine type from ```EngineType``` enum class
     * @return static Self object
     */
    public function for(int $engine, callable $callback): QueryInterface;

    /**
     * Execute query when condition is fulfilled
     * 
     * @param bool $condition Condition
     * @param callable|null $whenTrue Query when true
     * @param callable|null $whenFalse Query when false
     * @return static Self object
     */
    public function when(bool $condition, ?callable $whenTrue, ?callable $whenFalse): QueryInterface;
}
