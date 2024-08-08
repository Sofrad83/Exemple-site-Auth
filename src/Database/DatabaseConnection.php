<?php
namespace App\Database;
use Exception;
use PDO;
use PDOException;

class DatabaseConnection {
    private $pdo;
    private $config;

    public function __construct(DatabaseConfig $config) {
        $this->config = $config;
        $this->connect();
    }

    private function connect() {
        $dsn = 'mysql:host=' . $this->config->getHost() . ';dbname=' . $this->config->getDbName();
        $username = $this->config->getUsername();
        $password = $this->config->getPassword();

        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception('Database connection error: ' . $e->getMessage());
        }
    }

    public function getPdo() {
        return $this->pdo;
    }
}
