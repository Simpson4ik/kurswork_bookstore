<?php
/** @var string $title */
/** @var array $books */
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
</head>
<body>
<header>
    <h1>Панель керування інтернет-магазином</h1>
    <nav>
        <a href="/coursework/">На головну сайту</a> |
        <a href="/coursework/admin/book/add" style="font-weight: bold; color: green;">➕ Додати нову книгу</a>
        <a href="/coursework/admin/publishers" style="font-weight: bold; color: blue;">🏢 Видавництва</a> |
        <a href="/coursework/admin/authors" style="font-weight: bold; color: purple;">✍️ Автори</a> |
        <a href="/coursework/admin/genres" style="font-weight: bold; color: orange;">🎭 Жанри</a> ||
    </nav>
</header>
<main>
    <h2>Керування каталогом книг (CRUD)</h2>

    <?php if (!empty($books)): ?>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: left;">
            <thead>
            <tr>
                <th>ID</th>
                <th>Назва книги</th>
                <th>ISBN</th>
                <th>Ціна</th>
                <th>Кількість</th>
                <th>Видавництво</th>
                <th>Дії</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?php echo $book['book_id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($book['title']); ?></strong></td>
                    <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                    <td><?php echo htmlspecialchars($book['price']); ?> грн</td>
                    <td><?php echo $book['stock_quantity']; ?> шт.</td>
                    <td><?php echo htmlspecialchars($book['publisher_name']); ?></td>
                    <td>
                        <a href="/coursework/admin/book/edit/<?php echo $book['book_id']; ?>" style="color: blue; margin-right: 15px;">Редагувати</a>
                        <a href="/coursework/admin/book/delete/<?php echo $book['book_id']; ?>"
                           style="color: red;"
                           onclick="return confirm('Ви впевнені, що хочете видалити цю книгу з бази даних?')">Видалити</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>У каталозі поки немає жодної книги.</p>
    <?php endif; ?>
</main>
</body>
</html>