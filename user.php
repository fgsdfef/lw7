<?php

declare(strict_types=1);

class User
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function addUser(string $name, string $email): bool
    {
        if (!$this->validateName($name)) {
            throw new InvalidArgumentException("Имя должно содержать от 2 до 100 символов");
        }

        if (!$this->validateEmail($email)) {
            throw new InvalidArgumentException("Некорректный email адрес");
        }

        if ($this->db->emailExists($email)) {
            throw new InvalidArgumentException("Пользователь с таким email уже существует");
        }

        return $this->db->insertUser($name, $email);
    }

    private function validateName(string $name): bool
    {
        $name = trim($name);
        $length = mb_strlen($name);
        
        return $length >= 2 && $length <= 100;
    }

    private function validateEmail(string $email): bool
    {
        $email = trim($email);
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        if (mb_strlen($email) > 255) {
            return false;
        }
        
        return true;
    }

    public function getAllUsers(): array
    {
        return $this->db->getAllUsers();
    }

    public function displayUsers(): void
    {
        $users = $this->getAllUsers();
        
        if (empty($users)) {
            echo "В базе данных нет пользователей." . PHP_EOL;
            return;
        }

        echo str_pad("ID", 5) . " | " . 
             str_pad("Имя", 20) . " | " . 
             str_pad("Email", 30) . PHP_EOL;
        echo str_repeat("-", 60) . PHP_EOL;

        foreach ($users as $user) {
            echo str_pad((string)$user['id'], 5) . " | " . 
                 str_pad($user['name'], 20) . " | " . 
                 str_pad($user['email'], 30) . PHP_EOL;
        }
    }
}
