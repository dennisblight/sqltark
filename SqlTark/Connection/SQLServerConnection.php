<?php

declare(strict_types=1);

namespace SqlTark\Connection;

use PDO;

class SQLServerConnection extends AbstractConnection
{
    /** @var PDO $pdo */
    protected $pdo = null;

    protected const Driver = 'sqlsrv';

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
        $dsn = "sqlsrv:Server={$this->host}";
        if(!empty($this->port)) $dsn = "{$dsn},{$this->port}";
        $dsn = "{$dsn};Database={$this->database}";
        return $dsn;
    }

    public function trransaction(): bool
    {
        if($this->transactionCount++ === 0) {
            return $this->getPDO()->beginTransaction();
        }

        $this->getPDO()->exec("SAVE TRANSACTION __trx_{$this->transactionCount}__");
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
            $this->getPDO()->exec("ROLLBACK TRANSACTION __trx_{$this->transactionCount}__");
            $this->transactionCount--;
            return true;
        }

        $this->transactionCount--;
        return $this->getPDO()->rollBack();
    }
}