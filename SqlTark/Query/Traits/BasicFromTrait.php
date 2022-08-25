<?php

declare(strict_types=1);

namespace SqlTark\Query\Traits;

use InvalidArgumentException;
use SqlTark\Component\FromClause;
use SqlTark\Component\ComponentType;
use SqlTark\Helper;

trait BasicFromTrait
{
    /**
     * @var ?string $alias
     */
    protected $alias;

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return $this Self object
     */
    public function alias(?string $alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @return $this Self object
     */
    public function from($table, ?string $alias = null)
    {
        $component = null;
        if (is_string($table)) {
            $component = new FromClause;
            $component->setTable($table);
            $component->setAlias($alias);
        } else {
            $class = Helper::getType($table);
            throw new InvalidArgumentException("Could not resolve '$class' as table");
        }

        return $this->addOrReplaceComponent(ComponentType::From, $component);
    }
}
