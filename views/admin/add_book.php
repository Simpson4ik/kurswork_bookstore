<?php
/** @var string $title */
/** @var array $publishers */
/** @var array $authors */
/** @var array $genres */
?>
<h2>Додати нову книгу</h2>

<form id="add-book-form" style="max-width: 500px; margin: 0 auto;">
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
        <label for="cover_image">Обкладинка книги (JPG, PNG, WebP):</label>
        <input type="file" id="cover_image" name="cover_image" accept="image/jpeg,image/png,image/webp">
    </div>
    <div>
        <label for="publisher_search">Видавництво (пошук):</label>
        <input type="text" id="publisher_search" placeholder="Почніть вводити назву видавництва...">
        <label for="publisher_id">Обрати видавництво:</label>
        <select id="publisher_id" name="publisher_id" required>
            <option value="">-- Оберіть зі списку --</option>
            <?php foreach ($publishers as $publisher): ?>
                <option value="<?php echo $publisher['publisher_id']; ?>">
                    <?php echo htmlspecialchars($publisher['publisher_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom: 15px;">
        <label for="author_search"><strong>Автори книги (пошук):</strong></label>
        <input type="text" id="author_search" placeholder="Почніть вводити прізвище автора..." style="margin-bottom: 8px;">
        <div id="authors-checkbox-container" style="max-height: 150px; overflow-y: auto; border: 1px solid var(--border); padding: 10px; border-radius: 6px; background: var(--panel-galaxy);">
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
        <label for="genre_search"><strong>Жанри книги (пошук):</strong></label>
        <input type="text" id="genre_search" placeholder="Почніть вводити назву жанру..." style="margin-bottom: 8px;">
        <div id="genres-checkbox-container" style="max-height: 150px; overflow-y: auto; border: 1px solid var(--border); padding: 10px; border-radius: 6px; background: var(--panel-galaxy);">
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
<p><a href="/coursework/admin/dashboard">Повернутися в адмінку</a></p>