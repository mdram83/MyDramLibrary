<?php

namespace MyDramLibrary\Utilities\Database;

use PDO;
use PDOStatement;
use MyDramLibrary\Configuration\DatabaseConfiguration;

class Database
{
    protected static ?Database $instance = null;
    protected PDO $pdo;

    private function __construct()
    {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $this->pdo = new PDO(
            DatabaseConfiguration::DSN,
            DatabaseConfiguration::USERNAME,
            DatabaseConfiguration::PASSWORD,
            $options
        );
    }

    public static function instance(): Database
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __call(string $method, mixed $args): mixed
    {
        return call_user_func_array([$this->pdo, $method], $args);
    }

    public function run(string $sql, array $args = []): PDOStatement|false
    {
        if (!$args) {
            return $this->query($sql);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
}
