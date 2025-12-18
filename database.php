<?php

declare(strict_types=1);

class Database
{
    private ?PDO $connection = null;
    private array $config;

    public function __construct()
    {
        $this->config = require 'config/database.php';
        $this->connect();
    }

    private function connect(): void
    {
        try {
            $dsn = "mysql:host={$this->config['host']};dbname={$this->config['dbname']};charset={$this->config['charset']}";
            
            $this->connection = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }

    public function getAllUsers(): array
    {
        $sql = "SELECT id, name, email FROM users ORDER BY id";
        $stmt = $this->connection->query($sql);
        return $stmt->fetchAll();
    }

    public function insertUser(string $name, string $email): bool
    {
        $sql = "INSERT INTO users (name, email) VALUES (:name, :email)";
        $stmt = $this->connection->prepare($sql);
        
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email
        ]);
    }

    public function emailExists(string $email): bool
    {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        return $stmt->fetchColumn() > 0;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function __destruct()
    {
        $this->connection = null;
    }
}
