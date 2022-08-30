<?php

declare(strict_types=1);

namespace SqlTark\Connection;

use ArrayAccess;
use InvalidArgumentException;
use PDO;

abstract class AbstractConnection
{
    /** @var PDO $pdo */
    protected $pdo = null;

    protected const Driver = '';

    protected $host       = 'localhost';
    protected $port       = null;
    protected $username   = null;
    protected $password   = null;
    protected $database   = '';
    protected $charset    = 'utf8';
    protected $collation  = 'utf8_general_ci';
    protected $attributes = [];

    protected $fetchMode = PDO::FETCH_OBJ;
    protected $fetchClassName = null;
    protected $fetchCallback = null;

    public function getPDO()
    {
        return $this->pdo ?? $this->connect();
    }

    /**
     * @param ArrayAccess $config
     */
    public function __construct($config = [])
    {
        if(!in_array(static::Driver, PDO::getAvailableDrivers(), true)) {
            $driver = static::Driver;
            throw new InvalidArgumentException(
                "PDO driver '{$driver}' not available"
            );
        }

        $this->hydrate($config);
    }

    /**
     * @param ArrayAccess $config
     */
    protected function hydrate($config)
    {
        if(!empty($config['host']))
        {
            $this->host = $config['host'];
        }

        if(!empty($config['port']))
        {
            $this->port = (int) $config['port'];
        }

        if(!empty($config['username']))
        {
            $this->username = $config['username'];
        }

        if(!empty($config['password']))
        {
            $this->password = $config['password'];
        }

        if(!empty($config['database']))
        {
            $this->database = $config['database'];
        }

        if(!empty($config['charset']))
        {
            $this->charset = $config['charset'];
        }

        if(!empty($config['collation']))
        {
            $this->collation = $config['collation'];
        }
    }

    abstract protected function createDSN(): string;

    public function __destruct()
    {
        $this->pdo = null;
    }

    public function connect()
    {
        $dsn = $this->createDSN();

        $this->pdo = new PDO($dsn, $this->username, $this->password);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $this->fetchMode);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        foreach($this->attributes as $key => $value) {
            $this->pdo->setAttribute($key, $value);
        }
        $this->onConnected();

        return $this->pdo;
    }

    protected function onConnected()
    {
        // $this->pdo->exec("SET NAMES '$this->charset' COLLATE '$this->collation'");
        // $this->pdo->exec("SET CHARACTER SET '$this->charset'");
    }

    // public function getFetchMode(): int
    // {
    //     return $this->fetchMode;
    // }

    // /**
    //  * @return $this Self object
    //  */
    // public function setFetchMode(int $fetchMode = PDO::FETCH_OBJ)
    // {
    //     $this->fetchMode = $fetchMode;
    //     return $this;
    // }

    // /**
    //  * @return $this Self object
    //  */
    // public function setFetchClass(string $className)
    // {
    //     $this->fetchMode = PDO::FETCH_CLASS;
    //     $this->fetchClassName = $className;
    //     return $this;
    // }
}