<?php

declare(strict_types=1);

namespace SqlTark;

use InvalidArgumentException;
use PDOStatement;
use SqlTark\Query\Query;
use SqlTark\Query\InsertQuery;
use SqlTark\Compiler\BaseCompiler;
use SqlTark\Component\ComponentType;
use SqlTark\Connection\AbstractConnection;
use SqlTark\Query\DeleteQuery;
use SqlTark\Query\Interfaces\QueryInterface;
use SqlTark\Query\UpdateQuery;

class XQuery extends Query
{
    /**
     * @var AbstractConnection $connection
     */
    private $connection;

    /**
     * @var BaseCompiler $compiler
     */
    private $compiler;

    /**
     * @return AbstractConnection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return BaseCompiler
     */
    public function getCompiler()
    {
        return $this->compiler;
    }

    public function __construct($connection, $compiler)
    {
        $this->connection = $connection;
        $this->compiler = $compiler;
    }

    /**
     * @return PDOStatement Statement
     */
    public function prepare(string $sql)
    {
        $pdo = $this->connection->getPDO();
        return $pdo->prepare($sql);
    }

    /**
     * @return PDOStatement Statement
     */
    public function execute(string $sql)
    {
        $statement = $this->prepare($sql);
        $statement->execute();
        return $statement;
    }

    /**
     * @return PDOStatement Statement
     */
    public function executeQuery(QueryInterface $query)
    {
        if ($query instanceof Query) {
            $sql = $this->compiler->compileQuery($query);
        } elseif ($query instanceof InsertQuery) {
            $sql = $this->compiler->compileInsertQuery($query);
        } elseif ($query instanceof UpdateQuery) {
            $sql = $this->compiler->compileUpdateQuery($query);
        } elseif ($query instanceof DeleteQuery) {
            $sql = $this->compiler->compileDeleteQuery($query);
        }

        if (empty($sql)) {
            $class = Helper::getType($query);
            throw new InvalidArgumentException(
                "Could not resolve '$class'"
            );
        }

        return $this->execute($sql);
    }

    public function insert(iterable $columns, ?iterable $values = null)
    {
        $query = call_user_func_array('parent::insert', func_get_args());
        return $this->executeQuery($query);
    }

    public function insertQuery(Query $query, ?iterable $columns = null)
    {
        $query = call_user_func_array('parent::insertQuery', func_get_args());
        return $this->executeQuery($query);
    }

    /**
     * @param iterable|object $value
     */
    public function update($value)
    {
        $query = call_user_func_array('parent::update', func_get_args());
        return $this->executeQuery($query);
    }

    public function delete()
    {
        $query = call_user_func_array('parent::delete', func_get_args());
        return $this->executeQuery($query);
    }

    public function getOne()
    {
        $limitComponent = $this->getOneComponent(ComponentType::Limit);
        if (empty($limitComponent)) {
            $this->limit(1);
        }

        $statement = $this->executeQuery($this);
        $result = $statement->fetch();
        $statement->closeCursor();

        if (empty(($limitComponent))) {
            $this->clearComponents(ComponentType::Limit);
        } else {
            $this->addOrReplaceComponent(ComponentType::Limit, $limitComponent);
        }

        return $result;
    }

    public function getAll()
    {
        $statement = $this->executeQuery($this);
        $result = $statement->fetchAll();
        $statement->closeCursor();

        return $result;
    }

    public function getScalar(int $columnIndex = 0)
    {
        $limitComponent = $this->getOneComponent(ComponentType::Limit);
        if (empty($limitComponent)) {
            $this->limit(1);
        }

        $statement = $this->executeQuery($this);
        $result = $statement->fetchColumn($columnIndex);
        $statement->closeCursor();

        if (empty(($limitComponent))) {
            $this->clearComponents(ComponentType::Limit);
        } else {
            $this->addOrReplaceComponent(ComponentType::Limit, $limitComponent);
        }

        return $result;
    }
}
