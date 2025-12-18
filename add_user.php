<?php

declare(strict_types=1);

require_once 'Database.php';
require_once 'User.php';

function displayHelp(): void
{
    echo "Использование:" . PHP_EOL;
    echo "  php add_user.php --name=\"Имя пользователя\" --email=\"email@example.com\"" . PHP_EOL;
    echo "  php add_user.php --interactive" . PHP_EOL;
    echo "  php add_user.php --show" . PHP_EOL . PHP_EOL;
    echo "Параметры:" . PHP_EOL;
    echo "  --name=NAME         Имя пользователя (2-100 символов)" . PHP_EOL;
    echo "  --email=EMAIL       Email пользователя" . PHP_EOL;
    echo "  --interactive       Интерактивный режим" . PHP_EOL;
    echo "  --show              Показать всех пользователей" . PHP_EOL;
    echo "  --help              Показать эту справку" . PHP_EOL;
}

function interactiveMode(User $user): void
{
    echo "=== Интерактивное добавление пользователя ===" . PHP_EOL . PHP_EOL;
    
    while (true) {
        echo "Введите имя пользователя (или 'exit' для выхода): ";
        $name = trim(fgets(STDIN));
        
        if (strtolower($name) === 'exit') {
            break;
        }
        
        echo "Введите email: ";
        $email = trim(fgets(STDIN));
        
        try {
            if ($user->addUser($name, $email)) {
                echo "✅ Пользователь успешно добавлен!" . PHP_EOL;
            }
        } catch (Exception $e) {
            echo "❌ Ошибка: " . $e->getMessage() . PHP_EOL;
        }
        
        echo PHP_EOL;
    }
}

$options = getopt('', ['name:', 'email:', 'interactive', 'show', 'help']);

if (isset($options['help']) || empty($options)) {
    displayHelp();
    exit(0);
}

$user = new User();

if (isset($options['show'])) {
    echo "=== Список всех пользователей ===" . PHP_EOL . PHP_EOL;
    $user->displayUsers();
    exit(0);
}

if (isset($options['interactive'])) {
    interactiveMode($user);
    exit(0);
}

if (!isset($options['name']) || !isset($options['email'])) {
    echo "❌ Ошибка: необходимо указать имя и email" . PHP_EOL . PHP_EOL;
    displayHelp();
    exit(1);
}

try {
    $name = $options['name'];
    $email = $options['email'];
    
    if ($user->addUser($name, $email)) {
        echo "✅ Пользователь успешно добавлен!" . PHP_EOL . PHP_EOL;
        
        echo "Обновленный список пользователей:" . PHP_EOL;
        $user->displayUsers();
    }
} catch (Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
