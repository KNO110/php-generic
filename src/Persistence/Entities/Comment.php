<?php
namespace Insid\Acdemo\Persistence\Entities;

use Insid\Acdemo\Persistence\Utils\GenericEntity;

class Comment extends GenericEntity
{
    private int $article_id;
    private int $user_id;
    private string $content;

    protected static function getTableName(): string
    {
        return 'comments';
    }


    public function setArticleId(int $a): void { $this->article_id = $a; }
    public function getArticleId(): int         { return $this->article_id; }

    public function setUserId(int $u): void    { $this->user_id = $u; }
    public function getUserId(): int           { return $this->user_id; }

    public function setContent(string $c): void{ $this->content = $c; }
    public function getContent(): string       { return $this->content; }

    public function toArray(): array
    {
        return [
            'article_id' => $this->getArticleId(),
            'user_id'    => $this->getUserId(),
            'content'    => $this->getContent(),
        ];
    }


    public function getUser(): ?User
    {
        return User::findById($this->getUserId());
    }

    public function getArticle(): ?Article
    {
        return Article::findById($this->getArticleId());
    }
}
