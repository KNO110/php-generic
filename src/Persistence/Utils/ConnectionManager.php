<?php

namespace Insid\Acdemo\Persistence\Utils;

use PDO;
use PDOException;

class ConnectionManager
{
    private static ?PDO $pdo = null;
    private static $config = [
        'host' => 'localhost',
        'dbname' => 'blog',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ];

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $config = self::$config;
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

            try {
                self::$pdo = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Кидати винятки при помилках
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_CLASS,  // Повертаємо клас (автомапінг)
                    PDO::ATTR_EMULATE_PREPARES => false,  // Використовуємо справжні підготовлені запити
                    PDO::ATTR_PERSISTENT => false  // Не використовуємо постійні з'єднання
                ]);
            } catch (PDOException $e) {
                die("Database connection failed: ".$e->getMessage());
            }
        }
        return self::$pdo;
    }
}