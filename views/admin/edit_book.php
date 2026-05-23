<?php
/** @var string $title */
/** @var array $book */
/** @var array $publishers */
/** @var array $authors */
/** @var array $genres */
/** @var array $currentAuthors */
/** @var array $currentGenres */
?>
<h2>Редагувати: «<?php echo htmlspecialchars($book['title']); ?>»</h2>

<form id="edit-book-form" data-book-id="<?php echo $book['book_id']; ?>" style="max-width: 500px; margin: 0 auto;">
    <div>
        <label for="title">Назва книги:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
    </div>
    <div>
        <label for="isbn">ISBN:</label>
        <input type="text" id="isbn" name="isbn" value="<?php echo htmlspecialchars($book['isbn']); ?>" required>
    </div>
    <div>
        <label for="publication_year">Рік видання:</label>
        <input type="number" id="publication_year" name="publication_year" value="<?php echo $book['publication_year']; ?>" required>
    </div>
    <div>
        <label for="price">Ціна (грн):</label>
        <input type="number" step="0.01" id="price" name="price" value="<?php echo $book['price']; ?>" required>
    </div>
    <div>
        <label for="stock_quantity">Кількість на складі:</label>
        <input type="number" id="stock_quantity" name="stock_quantity" value="<?php echo $book['stock_quantity']; ?>" required>
    </div>
    <div>
        <label>Поточна обкладинка:</label>
        <br>
        <?php if (!empty($book['cover_image'])): ?>
            <img src="/coursework/public/uploads/<?php echo $book['cover_image']; ?>" alt="Обкладинка" style="max-width: 140px; border-radius: 8px; margin-bottom: 12px; display: block; border: 1px solid var(--border);">
        <?php else: ?>
            <p style="color: var(--text-muted); font-style: italic; margin-bottom: 12px;">Обкладинка відсутня</p>
        <?php endif; ?>
        <label for="cover_image">Замінити обкладинку (JPG, PNG, WebP):</label>
        <input type="file" id="cover_image" name="cover_image" accept="image/jpeg,image/png,image/webp">
    </div>

    <div style="margin-bottom: 20px;">
        <label for="publisher_search"><strong>Видавництво:</strong></label>
        <div class="dropdown-search-wrapper">
            <input type="text" id="publisher_search" placeholder="Клікніть або введіть текст для пошуку видавництва..." autocomplete="off" value="<?php echo htmlspecialchars($book['publisher_name']); ?>" required>
            <input type="hidden" id="publisher_id" name="publisher_id" value="<?php echo $book['publisher_id']; ?>">
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
                <?php foreach ($authors as $author): ?>
                    <?php
                    $fullName = $author['last_name'] . ' ' . $author['first_name'];
                    $isChecked = in_array($author['author_id'], $currentAuthors) ? 'checked' : '';
                    ?>
                    <label>
                        <input type="checkbox" name="authors[]" value="<?php echo $author['author_id']; ?>" class="author-item-checkbox" data-name="<?php echo htmlspecialchars($fullName); ?>" <?php echo $isChecked; ?>>
                        <?php echo htmlspecialchars($fullName); ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div style="margin-bottom: 25px;">
        <label><strong>Жанри книги:</strong></label>
        <div id="selected-genres-badges" class="tags-container"></div>
        <div class="dropdown-search-wrapper">
            <input type="text" id="genre_search" placeholder="Клікніть або введіть текст для пошуку жанрів..." autocomplete="off">
            <div id="genres-checkbox-container" class="dropdown-search-list">
                <?php foreach ($genres as $genre): ?>
                    <?php $isChecked = in_array($genre['genre_id'], $currentGenres) ? 'checked' : ''; ?>
                    <label>
                        <input type="checkbox" name="genres[]" value="<?php echo $genre['genre_id']; ?>" class="genre-item-checkbox" data-name="<?php echo htmlspecialchars($genre['genre_name']); ?>" <?php echo $isChecked; ?>>
                        <?php echo htmlspecialchars($genre['genre_name']); ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <button type="submit">Зберегти зміни</button>
</form>
<p><a href="/coursework/admin/dashboard">Повернутися в admin-панель</a></p>