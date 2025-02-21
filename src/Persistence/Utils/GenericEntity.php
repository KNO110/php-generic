<?php

namespace Insid\Acdemo\Persistence\Utils;

use PDO;
use ReflectionClass;

abstract class GenericEntity
{
    protected ?int $id = null;

    private static PDO $pdo;

    /**
     * @return array<static>
     */
    public static function findAll(): array
    {
        $table = static::getTableName();
        $statement = self::$pdo->query("SELECT * FROM `$table` ORDER BY id DESC");
        return $statement->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    public static function findById(int $id): ?static
    {
        $table = static::getTableName();
        $statement = self::$pdo->prepare("SELECT * FROM `$table` WHERE id = :id");
        $statement->execute([':id' => $id]);
        $statement->setFetchMode(PDO::FETCH_CLASS, static::class);
        return $statement->fetch() ?: null;
    }

    public function save(): bool
    {
        $table = static::getTableName();

        // Отримуємо всі властивості, перетворюючи їх у snake_case
        //$properties = get_object_vars($this);

        // Тут отримуємо навіть з приватних полів, хоча в Eloquent вони публічні
        $reflection = new ReflectionClass($this);

        $properties = [];
        foreach ($reflection->getProperties() as $property) {
            // Пропускаємо поле id та статичні властивості
            if ($property->getName() !== 'id' && !$property->isStatic()) {
                $property->setAccessible(true);
                $properties[$property->getName()] = $property->getValue($this);
            }
        }

        if ($this->id === null) {
            // Вставка нового запису
            $columns = implode(', ', array_keys($properties));
            $placeholders = implode(', ', array_map(fn($col) => ":$col", array_keys($properties)));
            $sql = "INSERT INTO `$table` ($columns) VALUES ($placeholders)";

            $stmt = self::$pdo->prepare($sql);
            $success = $stmt->execute($properties);
            if ($success) {
                $this->id = (int) self::$pdo->lastInsertId();
            }
            return $success;
        } else {
            // Оновлення існуючого запису
            $setClause = implode(', ', array_map(fn($col) => "$col = :$col", array_keys($properties)));
            $sql = "UPDATE `$table` SET $setClause WHERE id = :id";

            $stmt = self::$pdo->prepare($sql);
            $properties['id'] = $this->id;
            return $stmt->execute($properties);
        }
    }

    public function delete(): bool
    {
        if ($this->id === null) {
            return false; // Якщо немає ID, значить запису ще немає в БД
        }

        $table = static::getTableName();
        $stmt = self::$pdo->prepare("DELETE FROM `$table` WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public static function getPdo() {
        self::$pdo = ConnectionManager::getConnection();
        return self::$pdo;
    }

    abstract protected static function getTableName(): string;
}

// TODO: придумати спосіб, елегатніший ініціалізації обєкта PDO
GenericEntity::getPdo();