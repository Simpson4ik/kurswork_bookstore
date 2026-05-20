<?php
/** @var string $title */
/** @var array $book */
/** @var array $publishers */
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
    <h1>Панель керування — Редагування книги</h1>
    <nav>
        <a href="/coursework/admin/dashboard">Назад до списку</a>
    </nav>
</header>
<main>
    <h2>Редагувати: «<?php echo htmlspecialchars($book['title']); ?>»</h2>

    <form action="/coursework/admin/book/update/<?php echo $book['book_id']; ?>" method="POST">
        <div>
            <label for="title">Назва книги:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
        </div>
        <br>
        <div>
            <label for="isbn">ISBN:</label>
            <input type="text" id="isbn" name="isbn" value="<?php echo htmlspecialchars($book['isbn']); ?>" required>
        </div>
        <br>
        <div>
            <label for="publication_year">Рік видання:</label>
            <input type="number" id="publication_year" name="publication_year" value="<?php echo $book['publication_year']; ?>" required>
        </div>
        <br>
        <div>
            <label for="price">Ціна (грн):</label>
            <input type="number" step="0.01" id="price" name="price" value="<?php echo $book['price']; ?>" required>
        </div>
        <br>
        <div>
            <label for="stock_quantity">Кількість на складі:</label>
            <input type="number" id="stock_quantity" name="stock_quantity" value="<?php echo $book['stock_quantity']; ?>" required>
        </div>
        <br>
        <div>
            <label for="publisher_id">Видавництво:</label>
            <select id="publisher_id" name="publisher_id" required>
                <?php foreach ($publishers as $publisher): ?>
                    <option value="<?php echo $publisher['publisher_id']; ?>" <?php echo $publisher['publisher_id'] == $book['publisher_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($publisher['publisher_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <br>
        <button type="submit">Зберегти зміни</button>
    </form>
</main>
</body>
</html>