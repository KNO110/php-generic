<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Insid\Acdemo\Persistence\Utils\GenericEntity;
use Insid\Acdemo\Persistence\Entities\User;
use Insid\Acdemo\Persistence\Entities\Article;
use Insid\Acdemo\Persistence\Entities\Comment;
use Insid\Acdemo\Persistence\Entities\Tag;

function assertTrue(bool $c, string $m): void
{
    echo ($c ? '[PASS] ' : '[FAIL] ') . $m . PHP_EOL;
}

echo "=== START TESTS ===" . PHP_EOL;

echo "\n-- User --\n";
$user = new User();
$user->setName('Ivan');
$user->setEmail('ivan@example.com');
$user->setPassword('secret');
assertTrue($user->save(), 'User::save()');
assertTrue((bool)$user->getId(), 'User ID generated');
$uid = $user->getId();

$fU = User::findById($uid);
assertTrue($fU instanceof User, 'User found');
assertTrue($fU->getEmail() === 'ivan@example.com', 'Email matches');

$fU->setName('Ivan2');
assertTrue($fU->save(), 'User updated');
assertTrue(User::findById($uid)->getName() === 'Ivan2', 'Name updated');

echo "\n-- Article --\n";
$art = new Article();
$art->setUserId($uid);
$art->setTitle('Hello');
$art->setContent('World');
assertTrue($art->save(), 'Article::save()');
assertTrue((bool)$art->getId(), 'Article ID');
$aid = $art->getId();

$fA = Article::findById($aid);
assertTrue($fA->getUser() instanceof User, 'Article->getUser()');

echo "\n-- Tag --\n";
$tag = new Tag();
$tag->setName('php');
assertTrue($tag->save(), 'Tag::save()');
assertTrue((bool)$tag->getId(), 'Tag ID');
$tid = $tag->getId();

GenericEntity::getPdo()
    ->prepare("INSERT INTO article_tag(article_id,tag_id) VALUES(:a,:t)")
    ->execute(['a' => $aid, 't' => $tid]);

$tags = $fA->getTags();
assertTrue(count($tags) === 1, 'Article->getTags()');

$arts = $tag->getArticles();
assertTrue(count($arts) === 1, 'Tag->getArticles()');

echo "\n-- Comment --\n";
$cm = new Comment();
$cm->setUserId($uid);
$cm->setArticleId($aid);
$cm->setContent('Nice post!');
assertTrue($cm->save(), 'Comment::save()');
assertTrue((bool)$cm->getId(), 'Comment ID');
$cid = $cm->getId();

$fC = Comment::findById($cid);
assertTrue($fC->getUser() instanceof User, 'Comment->getUser()');
assertTrue($fC->getArticle() instanceof Article, 'Comment->getArticle()');

echo "\n=== TESTS COMPLETE ===\n";
