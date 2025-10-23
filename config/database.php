<?php

/**
 * Database Configuration
 * PDO connection with singleton pattern
 */

class Database
{
    private static $instance = null;
    private $connection;

    private $host = 'localhost';
    private $dbname = 'asaa_healthcare';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';

    private function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function __clone()
    {
        throw new Exception("Cannot clone singleton");
    }

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
