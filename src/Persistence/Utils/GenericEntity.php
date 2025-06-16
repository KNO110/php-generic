<?php

namespace Insid\Acdemo\Persistence\Utils;

use PDO;
use ReflectionClass;

abstract class GenericEntity
{
    protected ?int $id = null;
    private static PDO $pdo;

    public static function findAll(): array
    {
        $t    = static::getTableName();
        $stmt = self::getPdo()->query("SELECT * FROM `{$t}` ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    public static function findById(int $id): ?static
    {
        $t    = static::getTableName();
        $stmt = self::getPdo()->prepare("SELECT * FROM `{$t}` WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, static::class);
        return $stmt->fetch() ?: null;
    }

    public function save(): bool
    {
        $t   = static::getTableName();
        $ref = new ReflectionClass($this);
        $props = [];

        foreach ($ref->getProperties() as $p) {
            if ($p->isStatic() || $p->getName() === 'id') {
                continue;
            }
            $p->setAccessible(true);
            $val = $p->getValue($this);
            if ($val === null || $val === '') {
                continue;
            }
            $props[$p->getName()] = $val;
        }

        echo "DEBUG PARAMS: ";
        var_export($props);
        echo "\n";

        if ($this->id === null) {
            $cols    = implode(', ', array_keys($props));
            $holders = implode(', ', array_map(fn($c) => ":{$c}", array_keys($props)));
            $sql     = "INSERT INTO `{$t}` ({$cols}) VALUES ({$holders})";

            echo "DEBUG SQL: {$sql}\n";

            $stmt = self::getPdo()->prepare($sql);
            $ok   = $stmt->execute($props);
            if ($ok) {
                $this->id = (int) self::getPdo()->lastInsertId();
            }
            return $ok;
        }

        $set     = implode(', ', array_map(fn($c) => "{$c} = :{$c}", array_keys($props)));
        $sql     = "UPDATE `{$t}` SET {$set} WHERE id = :id";
        $props['id'] = $this->id;

        echo "DEBUG SQL: {$sql}\n";
        echo "DEBUG PARAMS: ";
        var_export($props);
        echo "\n";

        $stmt = self::getPdo()->prepare($sql);
        return $stmt->execute($props);
    }

    public function delete(): bool
    {
        if ($this->id === null) {
            return false;
        }
        $t    = static::getTableName();
        $stmt = self::getPdo()->prepare("DELETE FROM `{$t}` WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public static function getPdo(): PDO
    {
        if (!isset(self::$pdo)) {
            self::$pdo = ConnectionManager::getConnection();
        }
        return self::$pdo;
    }

    abstract protected static function getTableName(): string;
}
