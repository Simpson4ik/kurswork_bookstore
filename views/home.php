<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
</head>
<body>
<header>
    <h1>Книжковий інтернет-магазин</h1>
    <nav>
        <a href="/coursework/cart">🛒 Мій Кошик</a> |
        <a href="/coursework/login">Вхід</a>
    </nav>
</header>
<main>
    <h2>Каталог книг</h2>

    <?php if (!empty($books)): ?>
        <ul>
            <?php foreach ($books as $book): ?>
                <li>
                    <h3>
                        <a href="/coursework/book/<?php echo $book['book_id']; ?>">
                            <?php echo htmlspecialchars($book['title']); ?>
                        </a>
                    </h3>
                    <p>Рік видання: <?php echo htmlspecialchars($book['publication_year']); ?></p>
                    <p>Ціна: <?php echo htmlspecialchars($book['price']); ?> грн</p>
                    <p>В наявності: <?php echo htmlspecialchars($book['stock_quantity']); ?> шт.</p>
                    <a href="/coursework/cart/add/<?php echo $book['book_id']; ?>">
                        <button type="button">Додати в кошик</button>
                    </a>
                </li>
                <br>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Наразі книг немає в каталозі.</p>
    <?php endif; ?>
</main>
</body>
</html>