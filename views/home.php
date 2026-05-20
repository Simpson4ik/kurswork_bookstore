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
</header>
<main>
    <h2>Каталог книг</h2>

    <?php if (!empty($books)): ?>
        <ul>
            <?php foreach ($books as $book): ?>
                <li>
                    <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p>Рік видання: <?php echo htmlspecialchars($book['publication_year']); ?></p>
                    <p>Ціна: <?php echo htmlspecialchars($book['price']); ?> грн</p>
                    <p>В наявності: <?php echo htmlspecialchars($book['stock_quantity']); ?> шт.</p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Наразі книг немає в каталозі.</p>
    <?php endif; ?>
</main>
</body>
</html>