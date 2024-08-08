<?php
namespace App\Database;

class DatabaseConfig {
    private $host;
    private $dbname;
    private $username;
    private $password;

    public function __construct() {
        $this->host = $_ENV['DB_HOST'];
        $this->dbname = $_ENV['DB_NAME'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];
    }

    public function getHost() {
        return $this->host;
    }

    public function getDbName() {
        return $this->dbname;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }
}
