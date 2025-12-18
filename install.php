<?php

declare(strict_types=1);

require_once 'Database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    $sql = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($sql);
    
    echo "✅ Таблица 'users' успешно создана или уже существует." . PHP_EOL;
    
    // Добавим тестовые данные, если таблица пуста
    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    
    if ($count == 0) {
        $testUsers = [
            ['Иван Петров', 'ivan@example.com'],
            ['Мария Сидорова', 'maria@example.com'],
            ['Алексей Иванов', 'alexey@example.com']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        
        foreach ($testUsers as $user) {
            $stmt->execute([$user[0], $user[1]]);
        }
        
        echo "✅ Добавлены тестовые пользователи." . PHP_EOL;
    }
    
} catch (PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
