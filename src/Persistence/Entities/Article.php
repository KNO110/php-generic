<?php
namespace Insid\Acdemo\Persistence\Entities;

use Insid\Acdemo\Persistence\Utils\GenericEntity;
use PDO;

class Article extends GenericEntity
{
    private int $user_id;
    private string $title;
    private string $content;

    protected static function getTableName(): string
    {
        return 'articles';
    }


    public function setUserId(int $u): void  { $this->user_id = $u; }
    public function getUserId(): int         { return $this->user_id; }

    public function setTitle(string $t): void  { $this->title = $t; }
    public function getTitle(): string         { return $this->title; }

    public function setContent(string $c): void{ $this->content = $c; }
    public function getContent(): string       { return $this->content; }


    public function toArray(): array
    {
        return [
            'user_id' => $this->getUserId(),
            'title'   => $this->getTitle(),
            'content' => $this->getContent(),
        ];
    }


    public function getUser(): ?User
    {
        return User::findById($this->getUserId());
    }

    public function getComments(): array
    {
        return Comment::findAllBy(['article_id' => $this->getId()]);
    }

    public function getTags(): array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT tag_id FROM article_tag WHERE article_id = :aid");
        $stmt->execute(['aid' => $this->getId()]);
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_map(fn($i) => Tag::findById((int)$i), $ids);
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
