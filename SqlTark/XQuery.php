<?php

declare(strict_types=1);

namespace SqlTark;

use PDOStatement;
use SqlTark\Query\Query;
use InvalidArgumentException;
use SqlTark\Query\DeleteQuery;
use SqlTark\Query\InsertQuery;
use SqlTark\Query\UpdateQuery;
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
     * @var int $transactionCount
     */
    private $transactionCount = 0;

    /**
     * @var $logger
     */
    private $logger;

    public function getLogger()
    {
        return $this->logger;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
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

    public function __construct($connection, $compiler)
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
        return $this;
    }

    /**
     * @return PDOStatement Statement
     */
    public function prepare(string $sql)
    {
        $pdo = $this->connection->getPDO();
        if($this->resetOnExecute) {
            $this->reset();
        }
        return $pdo->prepare($sql);
    }

    /**
     * @return PDOStatement Statement
     */
    public function execute(string $sql)
    {
        try
        {
            $statement = $this->prepare($sql);
            $statement->execute();
            $this->log($sql, $statement->errorInfo());

            return $statement;
        }
        catch(\PDOException $ex)
        {
            $this->log($sql);
            throw $ex;
        }
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
        
        if(!$this->resetOnExecute) {
            if(empty($limitComponent)) {
                $this->clearComponents(ComponentType::Limit);
            } else {
                $this->addOrReplaceComponent(ComponentType::Limit, $limitComponent);
            }
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
     * @return bool|PDOStatement
     */
    public function transaction()
    {
        if($this->transactionCount++ === 0) {
            return $this->connection->getPDO()->beginTransaction();
        }

        $this->execute("SAVEPOINT __trx{$this->transactionCount}__");
        return $this->transactionCount >= 0;
    }

    /**
     * @return bool|PDOStatement
     */
    public function commit()
    {
        if(--$this->transactionCount === 0) {
            return $this->connection->getPDO()->commit();
        }

        return $this->transactionCount >= 0;
    }

    /**
     * @return bool|PDOStatement
     */
    public function rollback()
    {
        if($this->transactionCount > 1) {
            $this->execute("ROLLBACK TO __trx{$this->transactionCount}__");
            $this->transactionCount--;
            return true;
        }

        $this->transactionCount--;
        return $this->connection->getPDO()->rollBack();
    }

    private function log(string $sql, ?array $errorInfo = null)
    {
        if(!is_null($this->logger)) {
            $this->logger->log([
                'sql'        => $sql,
                'errorInfo'  => $errorInfo,
                'connection' => $this->connection->getConfig(),
            ]);
        }
    }
}
