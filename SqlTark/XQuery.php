<?php

declare(strict_types=1);

namespace SqlTark;

use PDO;
use PDOException;
use PDOStatement;
use SqlTark\Query\Query;
use InvalidArgumentException;
use SqlTark\Query\MethodType;
use SqlTark\Compiler\BaseCompiler;
use SqlTark\Component\ComponentType;
use SqlTark\Connection\AbstractConnection;
use SqlTark\Query\Interfaces\QueryInterface;

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
     * @var bool $resetOnExecute
     */
    private $resetOnExecute = true;

    /**
     * @var $onExecuteCallback
     */
    private $onExecuteCallback;

    public function onExecute(callable $onExecuteCallback)
    {
        $this->onExecuteCallback = $onExecuteCallback;
    }

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

    /**
     * @return $this Self object
     */
    public function resetOnExecute(bool $value = true)
    {
        $this->resetOnExecute = $value;
        return $this;
    }

    public function __construct(?AbstractConnection $connection = null, ?BaseCompiler $compiler = null)
    {
        $this->connection = $connection;
        $this->compiler = $compiler;
    }

    /**
     * @return $this Self object
     */
    public function reset()
    {
        if(!is_null($this->components)) {
            $this->components->removeAll($this->components);
            $this->method = MethodType::Select;
        }
        return $this;
    }

    public function compile(?QueryInterface $query = null, int $method = MethodType::Auto): ?string 
    {
        return $this->compiler->compileQuery($query ?? $this, $method);
    }

    /**
     * @return PDOStatement Statement
     */
    public function prepare($query = null, array $params = [], array $types = [])
    {
        if(func_num_args() === 0) {
            $query = $this->compiler->compileQuery($this);
        }

        if ($query instanceof QueryInterface) {
            $sql = $this->compiler->compileQuery($query);
        }
        elseif (is_string($query)) {
            $sql = $query;
        }

        if (empty($sql)) {
            $class = Helper::getType($query);
            throw new InvalidArgumentException(
                "Could not resolve '$class'"
            );
        }

        $pdo = $this->connection->getPDO();
        if($this->resetOnExecute) {
            $this->reset();
        }

        $statement = $pdo->prepare($sql);
        foreach($params as $index => $value) {
            $type = $this->determineType($index, $value, $types);
            $statement->bindValue($index, $value, $type);
        }

        return $statement;
    }

    private function determineType($index, $value, array $types)
    {
        if(array_key_exists($index, $types)) {
            return $types[$index];
        }

        switch(Helper::getType($value)) {
            case 'bool':
            return PDO::PARAM_BOOL;
            
            case 'integer':
            return PDO::PARAM_INT;
            
            case 'null':
            return PDO::PARAM_NULL;
        }

        return PDO::PARAM_STR;
    }

    /**
     * @return PDOStatement Statement
     */
    public function execute($query = null, array $params = [], array $types = [])
    {
        if(func_num_args() === 0) {
            $query = $this->compiler->compileQuery($query ?? $this);
        }

        if ($query instanceof QueryInterface) {
            $sql = $this->compiler->compileQuery($query);
        }
        elseif (is_string($query)) {
            $sql = $query;
        }

        if (empty($sql)) {
            $class = Helper::getType($query);
            throw new InvalidArgumentException(
                "Could not resolve '$class'"
            );
        }

        try {
            $statement = $this->prepare($sql, $params, $types);
            $statement->execute();
            $this->triggerOnExecute($sql, $statement->errorInfo(), $statement);

            return $statement;
        }
        catch(PDOException $ex) {
            if(isset($statement)) {
                $this->triggerOnExecute($sql, $statement->errorInfo(), $statement);
            }
            else {
                $this->triggerOnExecute($sql);
            }
            throw $ex;
        }
    }

    public function insert(iterable $columns, ?iterable $values = null)
    {
        $query = call_user_func_array('parent::asInsert', func_get_args());
        return $this->execute($query);
    }

    public function insertQuery(Query $query, ?iterable $columns = null)
    {
        $query = call_user_func_array('parent::asInsertQuery', func_get_args());
        return $this->execute($query);
    }

    /**
     * @param iterable|object $value
     */
    public function update($value)
    {
        $query = call_user_func_array('parent::asUpdate', func_get_args());
        return $this->execute($query);
    }

    public function delete()
    {
        $query = call_user_func_array('parent::asDelete', func_get_args());
        return $this->execute($query);
    }

    public function getOne($fetchMode = PDO::FETCH_OBJ)
    {
        $limitComponent = $this->getOneComponent(ComponentType::Limit);
        if (empty($limitComponent)) {
            $this->limit(1);
        }

        $lastMethod = $this->method;
        $this->method = MethodType::Select;

        $statement = $this->execute($this);
        if(is_string($fetchMode)) {
            $statement->setFetchMode(PDO::FETCH_CLASS, $fetchMode);
        }
        else {
            $statement->setFetchMode($fetchMode);
        }
        $result = $statement->fetch();
        $statement->closeCursor();

        $this->method = $lastMethod;

        if(!$this->resetOnExecute) {
            if(empty($limitComponent)) {
                $this->clearComponents(ComponentType::Limit);
            } else {
                $this->addOrReplaceComponent(ComponentType::Limit, $limitComponent);
            }
        }

        return $result;
    }

    public function getAll($fetchMode = PDO::FETCH_OBJ)
    {
        $lastMethod = $this->method;
        $this->method = MethodType::Select;

        $statement = $this->execute($this);
        if(is_string($fetchMode)) {
            $statement->setFetchMode(PDO::FETCH_CLASS, $fetchMode);
        }
        else {
            $statement->setFetchMode($fetchMode);
        }
        $result = $statement->fetchAll();
        $statement->closeCursor();

        $this->method = $lastMethod;

        return $result;
    }

    public function getScalar(int $columnIndex = 0)
    {
        $limitComponent = $this->getOneComponent(ComponentType::Limit);
        if (empty($limitComponent)) {
            $this->limit(1);
        }

        $lastMethod = $this->method;
        $this->method = MethodType::Select;
        
        $statement = $this->execute($this);
        $result = $statement->fetchColumn($columnIndex);
        $statement->closeCursor();

        $this->method = $lastMethod;
        
        if(!$this->resetOnExecute) {
            if(empty($limitComponent)) {
                $this->clearComponents(ComponentType::Limit);
            } else {
                $this->addOrReplaceComponent(ComponentType::Limit, $limitComponent);
            }
        }

        return $result;
    }

    public function lastInsertId(?string $name)
    {
        return $this->connection->getPDO()->lastInsertId($name);
    }

    /**
     * @return bool
     */
    public function transaction(): bool
    {
        return $this->connection->transaction();
    }

    /**
     * @return bool
     */
    public function commit(): bool
    {
        return $this->connection->commit();
    }

    /**
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->connection->rollback();
    }

    /**
     * @return XQuery
     */
    public function newQuery()
    {
        $self = new XQuery($this->connection, $this->compiler);
        $self->onExecuteCallback = $this->onExecuteCallback;
        return $self;
    }

    /**
     * @return static Clone of current object
     */
    public function __clone()
    {
        /** @var static $clone */
        $self = parent::__clone();
        $self->onExecuteCallback = $this->onExecuteCallback;
        $self->connection = $this->connection;
        $self->compiler = $this->compiler;

        return $self;
    }

    private function triggerOnExecute(string $sql, ?array $errorInfo = null, ?PDOStatement $statement = null)
    {
        if(is_callable($this->onExecuteCallback)) {
            return call_user_func_array($this->onExecuteCallback, [$sql, $errorInfo, $statement]);
        }
    }
}
