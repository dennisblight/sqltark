<?php

declare(strict_types=1);

namespace SqlTark\Connection;

use ArrayAccess;
use InvalidArgumentException;
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

    public function getPDO()
    {
        return $this->pdo ?? $this->connect();
    }

    protected function createDSN(): string
    {
        $dsn = "mysql:host=$this->host";
        if(!empty($this->port)) $dsn = "$dsn:$this->port;port=$this->port";
        $dsn = "$dsn;dbname=$this->database";
        return $dsn;
    }

    protected function onConnected()
    {
        $this->pdo->exec("SET NAMES '$this->charset' COLLATE '$this->collation'");
        $this->pdo->exec("SET CHARACTER SET '$this->charset'");
    }
}