<?php
/** @var string $title */
/** @var array $publishers */
/** @var array $authors */
/** @var array $genres */
?>
<h2>Додати нову книгу</h2>

<form action="/coursework/admin/book/store" method="POST" style="max-width: 500px; margin: 0 auto;">
    <div>
        <label for="title">Назва книги:</label>
        <input type="text" id="title" name="title" required>
    </div>
    <div>
        <label for="isbn">ISBN:</label>
        <input type="text" id="isbn" name="isbn" required>
    </div>
    <div>
        <label for="publication_year">Рік видання:</label>
        <input type="number" id="publication_year" name="publication_year" required>
    </div>
    <div>
        <label for="price">Ціна (грн):</label>
        <input type="number" step="0.01" id="price" name="price" required>
    </div>
    <div>
        <label for="stock_quantity">Кількість на складі:</label>
        <input type="number" id="stock_quantity" name="stock_quantity" required>
    </div>
    <div>
        <label for="publisher_id">Видавництво:</label>
        <select id="publisher_id" name="publisher_id" required>
            <?php foreach ($publishers as $publisher): ?>
                <option value="<?php echo $publisher['publisher_id']; ?>">
                    <?php echo htmlspecialchars($publisher['publisher_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom: 15px;">
        <label><strong>Автори книги (можна обрати декілька):</strong></label>
        <div style="max-height: 120px; overflow-y: auto; border: 1px solid var(--border); padding: 10px; border-radius: 6px; background: #fff;">
            <?php if(!empty($authors)): ?>
                <?php foreach ($authors as $author): ?>
                    <label style="display: block; margin-bottom: 6px; font-weight: normal;">
                        <input type="checkbox" name="authors[]" value="<?php echo $author['author_id']; ?>">
                        <?php echo htmlspecialchars($author['last_name'] . ' ' . $author['first_name']); ?>
                    </label>
                <?php endforeach; ?>
            <?php else: ?>
                <span style="color: red; font-size: 14px;">Спочатку додайте авторів в довідник!</span>
            <?php endif; ?>
        </div>
    </div>

    <div style="margin-bottom: 20px;">
        <label><strong>Жанри книги (можна обрати декілька):</strong></label>
        <div style="max-height: 120px; overflow-y: auto; border: 1px solid var(--border); padding: 10px; border-radius: 6px; background: #fff;">
            <?php if(!empty($genres)): ?>
                <?php foreach ($genres as $genre): ?>
                    <label style="display: block; margin-bottom: 6px; font-weight: normal;">
                        <input type="checkbox" name="genres[]" value="<?php echo $genre['genre_id']; ?>">
                        <?php echo htmlspecialchars($genre['genre_name']); ?>
                    </label>
                <?php endforeach; ?>
            <?php else: ?>
                <span style="color: red; font-size: 14px;">Спочатку додайте жанри в довідник!</span>
            <?php endif; ?>
        </div>
    </div>

    <button type="submit">Зберегти книгу</button>
</form>