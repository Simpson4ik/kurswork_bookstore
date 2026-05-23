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

    <div style="margin-bottom: 20px;">
        <label for="publisher_search"><strong>Видавництво:</strong></label>
        <div class="dropdown-search-wrapper">
            <input type="text" id="publisher_search" placeholder="Клікніть або введіть текст для пошуку видавництва..." autocomplete="off" required>
            <input type="hidden" id="publisher_id" name="publisher_id" value="">
            <div id="publishers-list-container" class="dropdown-search-list">
                <?php foreach ($publishers as $publisher): ?>
                    <div class="publisher-item" data-id="<?php echo $publisher['publisher_id']; ?>" data-name="<?php echo htmlspecialchars($publisher['publisher_name']); ?>" style="padding: 8px; cursor: pointer; border-radius: 6px; transition: background 0.2s;">
                        <?php echo htmlspecialchars($publisher['publisher_name']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div style="margin-bottom: 20px;">
        <label><strong>Автори книги:</strong></label>
        <div id="selected-authors-badges" class="tags-container"></div>
        <div class="dropdown-search-wrapper">
            <input type="text" id="author_search" placeholder="Клікніть або введіть текст для пошуку авторів..." autocomplete="off">
            <div id="authors-checkbox-container" class="dropdown-search-list">
                <?php if(!empty($authors)): ?>
                    <?php foreach ($authors as $author): ?>
                        <?php $fullName = $author['last_name'] . ' ' . $author['first_name']; ?>
                        <label>
                            <input type="checkbox" name="authors[]" value="<?php echo $author['author_id']; ?>" class="author-item-checkbox" data-name="<?php echo htmlspecialchars($fullName); ?>">
                            <?php echo htmlspecialchars($fullName); ?>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span style="color: red; font-size: 14px; padding: 6px; display: block;">Спочатку додайте авторів в довідник!</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div style="margin-bottom: 25px;">
        <label><strong>Жанри книги:</strong></label>
        <div id="selected-genres-badges" class="tags-container"></div>
        <div class="dropdown-search-wrapper">
            <input type="text" id="genre_search" placeholder="Клікніть або введіть текст для пошуку жанрів..." autocomplete="off">
            <div id="genres-checkbox-container" class="dropdown-search-list">
                <?php if(!empty($genres)): ?>
                    <?php foreach ($genres as $genre): ?>
                        <label>
                            <input type="checkbox" name="genres[]" value="<?php echo $genre['genre_id']; ?>" class="genre-item-checkbox" data-name="<?php echo htmlspecialchars($genre['genre_name']); ?>">
                            <?php echo htmlspecialchars($genre['genre_name']); ?>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span style="color: red; font-size: 14px; padding: 6px; display: block;">Спочатку додайте жанри в довідник!</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <button type="submit">Зберегти книгу</button>
</form>
<p><a href="/coursework/admin/dashboard">Повернутися в адмінку</a></p>