<?php
namespace Insid\Acdemo\Persistence\Entities;

use Insid\Acdemo\Persistence\Utils\GenericEntity;

class Tag extends GenericEntity
{
    private string $name;

    protected static function getTableName(): string
    {
        return 'tags';
    }

    public function setName(string $n): void { $this->name = $n; }
    public function getName(): string        { return $this->name; }

    public function toArray(): array
    {
        return ['name' => $this->getName()];
    }

    public function getArticles(): array
    {
        $pdo  = self::getPdo();
        $stmt = $pdo->prepare("SELECT article_id FROM article_tag WHERE tag_id = :tid");
        $stmt->execute(['tid' => $this->getId()]);
        $ids = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        return array_map(fn($i) => Article::findById((int)$i), $ids);
    }
}
