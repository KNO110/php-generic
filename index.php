<?php

declare(strict_types=1);

use Insid\Acdemo\Persistence\Entities\Article;
use Insid\Acdemo\Persistence\Entities\Comment;

require_once __DIR__ . '/vendor/autoload.php';

// Функція для виведення результатів
function assertTrue(bool $condition, string $message): void
{
    echo $condition ? "[PASS] $message\n" : "[FAIL] $message\n";
}

// Тестування всіх методів
echo "=== Тестування методів GenericEntity для класу Comment ===\n";

// 1. Тест save() - створення нового коментаря
$comment = new Comment();
$comment->setArticleId(1);
$comment->setContent("This is a test comment");
$comment->setCreatedAt(date('Y-m-d H:i:s'));
$result = $comment->save();
assertTrue($result, "Новий коментар успішно збережено");
assertTrue($comment->getId() !== null, "ID коментаря згенеровано після збереження");
$commentId = $comment->getId();

// 2. Тест findById()
$foundComment = Comment::findById($commentId);
assertTrue($foundComment !== null, "Коментар знайдено за ID");
assertTrue($foundComment->getContent() === "This is a test comment", "Зміст коментаря збігається");

// 3. Тест save() - оновлення коментаря
$foundComment->setContent("Updated test comment");
$foundComment->setUpdatedAt(date('Y-m-d H:i:s'));
$result = $foundComment->save();
assertTrue($result, "Коментар успішно оновлено");
$updatedComment = Comment::findById($commentId);
assertTrue($updatedComment->getContent() === "Updated test comment", "Зміст коментаря оновлено");
assertTrue($updatedComment->getUpdatedAt() !== null, "Updated_at оновлено");

// 4. Тест findAll()
$allComments = Comment::findAll();
assertTrue(count($allComments) > 0, "Список коментарів не порожній");
assertTrue($allComments[0]->getId() === $commentId, "Останній доданий коментар є першим у списку (ORDER BY id DESC)");

// 5. Тест delete()
$result = $updatedComment->delete();
assertTrue($result, "Коментар успішно видалено");
$deletedComment = Comment::findById($commentId);
assertTrue($deletedComment === null, "Коментар більше не існує в базі");

echo "=== Тестування завершено ===\n";