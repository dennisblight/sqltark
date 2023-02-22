<?php

declare(strict_types=1);

namespace SqlTark;

use PDO;
use PDOStatement;
use SqlTark\Query\Query;
use InvalidArgumentException;
use SqlTark\Query\MethodType;
use SqlTark\Compiler\BaseCompiler;
use SqlTark\Connection\AbstractConnection;
use SqlTark\Query\Interfaces\QueryInterface;

class XQuery extends Query
{
    /**
     * @var ?AbstractConnection $connection
     */
    private $connection;

    /**
     * @var ?BaseCompiler $compiler
     */
    private $compiler;

    /**
     * @var bool $resetOnExecute
     */
    private $resetOnExecute = true;

    /**
     * @var ?callable $onExecuteCallback
     */
    private $onExecuteCallback;

    /**
     * @var ?int $fetchMode
     */
    private $fetchMode;

    /**
     * @var ?array<mixed> $fetchModeParams
     */
    private $fetchModeParams;

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
     * @return $this
     */
    public function setConnection(AbstractConnection $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @return BaseCompiler
     */
    public function getCompiler()
    {
        return $this->compiler;
    }

    /**
     * @return $this
     */
    public function setCompiler(BaseCompiler $compiler)
    {
        $this->compiler = $compiler;
        return $this;
    }

    /**
     * @return ?int
     */
    public function getFetchMode()
    {
        return $this->fetchMode;
    }

    /**
     * @return ?array
     */
    public function getFetchModeParams()
    {
        return $this->fetchModeParams;
    }

    /**
     * @param int $fetchMode
     * @return $this
     */
    public function setFetchMode($fetchMode, ...$params)
    {
        $this->fetchMode = $fetchMode;
        $this->fetchModeParams = $params;
        return $this;
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
        }

        $this->fetchMode = null;
        $this->fetchModeParams = null;
        $this->method = MethodType::Select;

        return $this;
    }

    public function compile(?QueryInterface $query = null, int $method = MethodType::Auto): ?string 
    {
        return $this->compiler->compileQuery($query ?? $this, $method);
    }

    /**
     * @return PDOStatement Statement
     */
    public function prepare($query = null, array $params = [], array $types = []): PDOStatement
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

        try {
            $pdo = $this->connection->getPDO();

            $statement = $pdo->prepare($sql);
            foreach($params as $index => $value) {
                $type = $this->determineType($index, $value, $types);
                $statement->bindValue($index, $value, $type);
            }

            return $statement;
        }
        finally {
            if($this->resetOnExecute) {
                $this->reset();
            }
        }
    }

    private function determineType($index, $value, array $types): int
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
    public function execute($query = null, array $params = [], array $types = []): PDOStatement
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
        finally {
            if(isset($statement)) {
                $this->triggerOnExecute($sql, $statement->errorInfo(), $statement);
            }
            else {
                $this->triggerOnExecute($sql);
            }
        }
    }

    public function insert(iterable $columns, ?iterable $values = null): PDOStatement
    {
        $query = call_user_func_array('parent::asInsert', func_get_args());
        return $this->execute($query);
    }

    public function insertQuery(Query $query, ?iterable $columns = null): PDOStatement
    {
        $query = call_user_func_array('parent::asInsertQuery', func_get_args());
        return $this->execute($query);
    }

    /**
     * @param iterable|object $value
     */
    public function update($value): PDOStatement
    {
        $query = call_user_func_array('parent::asUpdate', func_get_args());
        return $this->execute($query);
    }

    public function delete(): PDOStatement
    {
        $query = call_user_func_array('parent::asDelete', func_get_args());
        return $this->execute($query);
    }

    public function getOne()
    {
        $this->method = MethodType::Select;

        $statement = $this->limit(1)->execute($this);
        if(!is_null($this->fetchMode) && $this->fetchMode !== PDO::FETCH_FUNC) {
            $statement->setFetchMode($this->fetchMode, ...$this->fetchModeParams);
        }

        $result = $statement->fetch();
        $statement->closeCursor();

        if($this->fetchMode === PDO::FETCH_FUNC
            && isset($this->fetchModeParams[0]) > 0
            && is_callable($this->fetchModeParams[0]))
            $result = ($this->fetchModeParams[0])($result);

        return $result;
    }

    public function getAll()
    {
        $this->method = MethodType::Select;

        $statement = $this->execute($this);
        if(!is_null($this->fetchMode)) {
            $statement->setFetchMode($this->fetchMode, ...$this->fetchModeParams);
        }

        $result = $statement->fetchAll();
        $statement->closeCursor();

        return $result;
    }

    public function getScalar(int $columnIndex = 0)
    {
        $this->method = MethodType::Select;

        $statement = $this->execute($this);
        $result = $statement->fetchColumn($columnIndex);
        $statement->closeCursor();

        return $result;
    }

    /**
     * @param string $name
     * [optional] Name of the sequence object from which the ID should be returned.
     * 
     * @return string|false
     * If a sequence name was not specified for the name parameter, PDO::lastInsertId
     * returns a string representing the row ID of the last row that was inserted
     * into the database.
     */
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

    private function triggerOnExecute(string $sql, ?array $errorInfo = null, ?PDOStatement $statement = null)
    {
        if(is_callable($this->onExecuteCallback)) {
            return call_user_func_array($this->onExecuteCallback, [$sql, $errorInfo, $statement]);
        }
    }
}
