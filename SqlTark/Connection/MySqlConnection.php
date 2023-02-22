<?php

declare(strict_types=1);

namespace SqlTark\Connection;

use PDO;

class MySqlConnection extends AbstractConnection
{
    /** @var PDO $pdo */
    protected $pdo = null;

    protected const Driver = 'mysql';

    protected $host       = 'localhost';
    protected $port       = 3306;
    protected $username   = 'root';
    protected $password   = null;
    protected $database   = '';
    protected $attributes = [];

    public function getPDO(): PDO
    {
        return $this->pdo ?? $this->connect();
    }

    protected function createDSN(): string
    {
        $dsn = "mysql:host={$this->host}";
        if(!empty($this->port)) $dsn = "{$dsn}:{$this->port};port={$this->port}";
        $dsn = "{$dsn};dbname={$this->database}";
        return $dsn;
    }

    protected function onConnected(): void
    {
        $this->pdo->exec("SET NAMES '{$this->charset}' COLLATE '{$this->collation}'");
        $this->pdo->exec("SET CHARACTER SET '{$this->charset}'");
    }

    public function trransaction(): bool
    {
        if($this->transactionCount++ === 0) {
            return $this->getPDO()->beginTransaction();
        }

        $this->getPDO()->exec("SAVEPOINT __trx_{$this->transactionCount}__");
        return $this->transactionCount >= 0;
    }

    public function commit(): bool
    {
        if(--$this->transactionCount === 0) {
            return $this->getPDO()->commit();
        }

        return $this->transactionCount >= 0;
    }

    public function rollback(): bool
    {
        if($this->transactionCount > 1) {
            $this->getPDO()->exec("ROLLBACK TO __trx_{$this->transactionCount}__");
            $this->transactionCount--;
            return true;
        }

        $this->transactionCount--;
        return $this->getPDO()->rollBack();
    }
}