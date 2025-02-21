<?php

namespace Insid\Acdemo\Persistence\Entities;

use Insid\Acdemo\Persistence\Utils\GenericEntity;

class Comment extends GenericEntity
{
    private int $article_id;
    private string $content;
    private string $created_at = '';
    private ?string $updated_at = null;

    protected static function getTableName(): string
    {
        return 'comments';
    }

    public function getArticleId(): int
    {
        return $this->article_id;
    }

    public function setArticleId(int $article_id): void
    {
        $this->article_id = $article_id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }
}