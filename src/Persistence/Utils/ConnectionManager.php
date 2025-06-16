<?php
namespace Insid\Acdemo\Persistence\Utils;

use PDO;
use PDOException;

class ConnectionManager
{
    private static ?PDO $pdo = null;

    private static array $config = [
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'dbname'   => 'blog',
        'username' => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
    ];

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $c   = self::$config;
            $dsn = "mysql:host={$c['host']};dbname={$c['dbname']};charset={$c['charset']}";
            try {
                self::$pdo = new PDO(
                    $dsn,
                    $c['username'],
                    $c['password'],
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {
                die("DB Connect Error: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
