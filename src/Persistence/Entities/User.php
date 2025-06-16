<?php
namespace Insid\Acdemo\Persistence\Entities;

use Insid\Acdemo\Persistence\Utils\GenericEntity;
use PDO;

class User extends GenericEntity
{
    private string $name;
    private string $email;
    private string $password;

    protected static function getTableName(): string
    {
        return 'users';
    }


    public function setName(string $n): void    { $this->name = $n; }
    public function getName(): string            { return $this->name; }

    public function setEmail(string $e): void    { $this->email = $e; }
    public function getEmail(): string           { return $this->email; }

    public function setPassword(string $pw): void
    {
        $this->password = password_hash($pw, PASSWORD_DEFAULT);
    }
    public function getPassword(): string        { return $this->password; }


    public function toArray(): array
    {
        return [
            'name'     => $this->getName(),
            'email'    => $this->getEmail(),
            'password' => $this->getPassword(),
        ];
    }


    public function getArticles(): array
    {
        return Article::findAllBy(['user_id' => $this->getId()]);
    }

    public function getComments(): array
    {
        return Comment::findAllBy(['user_id' => $this->getId()]);
    }

    public static function findAllBy(array $conds): array
    {
        $t = static::getTableName();
        $cl = implode(' AND ', array_map(fn($k) => "`{$k}` = :{$k}", array_keys($conds)));
        $sql = "SELECT * FROM `{$t}` WHERE {$cl} ORDER BY id DESC";
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($conds);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
    }
}
