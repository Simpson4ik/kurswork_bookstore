<?php
/** @var string $title */
/** @var array $books */
/** @var array $genres */
/** @var int $currentPage */
/** @var int $totalPages */
?>
<div class="catalog-header-section">
    <div class="catalog-title-group">
        <h2>🌌 Всесвіт Книг</h2>
        <p>Знайдіть свою наступну міжгалактичну подорож серед тисяч видань</p>
    </div>
    <div class="search-catalog-wrapper">
        <div class="search-input-relative">
            <input type="text" id="catalog-live-search" placeholder="Швидкий пошук книги, автора або жанру..." autocomplete="off">
            <span class="search-icon">🔍</span>
        </div>
    </div>
</div>

<div class="catalog-layout">
    <aside class="catalog-sidebar">
        <h3>🎛️ Фільтрація простору</h3>

        <div class="filter-section">
            <h4>Ціна (грн)</h4>
            <div class="price-range-inputs">
                <input type="number" id="filter-min-price" placeholder="Від" min="0">
                <span class="price-separator">&mdash;</span>
                <input type="number" id="filter-max-price" placeholder="До" min="0">
            </div>
        </div>

        <div class="filter-section">
            <h4>Жанри літератури</h4>
            <div class="filter-scroll-container">
                <?php foreach ($genres as $genre): ?>
                    <label class="filter-checkbox-label">
                        <input type="checkbox" class="filter-genre-checkbox" value="<?php echo $genre['genre_id']; ?>">
                        <span class="custom-checkbox-text"><?php echo htmlspecialchars($genre['genre_name']); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="filter-section stock-toggle-section">
            <label class="filter-checkbox-label">
                <input type="checkbox" id="filter-in-stock">
                <span class="custom-checkbox-text">Тільки в наявності</span>
            </label>
        </div>
    </aside>

    <main class="catalog-main">
        <?php if (!empty($books)): ?>
            <ul class="books-grid">
                <?php foreach ($books as $book): ?>
                    <li class="book-card">
                        <div class="book-cover-container">
                            <?php if (!empty($book['cover_image'])): ?>
                                <img src="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/public/uploads/<?php echo $book['cover_image']; ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="book-cover">
                            <?php else: ?>
                                <div class="book-cover-placeholder">📚 Обкладинка відсутня</div>
                            <?php endif; ?>
                        </div>
                        <h3>
                            <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/book/<?php echo $book['book_id']; ?>">
                                <?php echo htmlspecialchars($book['title']); ?>
                            </a>
                        </h3>
                        <p><strong>Автор(и):</strong> <?php echo htmlspecialchars($book['authors_list'] ?: 'Не вказано'); ?></p>
                        <p><strong>Жанр(и):</strong> <?php echo htmlspecialchars($book['genres_list'] ?: 'Не вказано'); ?></p>
                        <p>Рік видання: <?php echo htmlspecialchars($book['publication_year']); ?></p>
                        <p>Ціна: <span class="book-card-price"><?php echo htmlspecialchars($book['price']); ?> грн</span></p>
                        <p>В наявності: <?php echo htmlspecialchars($book['stock_quantity']); ?> шт.</p>
                        <button type="button" class="btn-add-to-cart" data-book-id="<?php echo $book['book_id']; ?>">
                            Додати в кошик
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($currentPage > 1): ?>
                        <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/?page=<?php echo $currentPage - 1; ?>" class="page-link">&larr; Назад</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/?page=<?php echo $i; ?>" class="page-link <?php echo $i === $currentPage ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/?page=<?php echo $currentPage + 1; ?>" class="page-link">Вперед &rarr;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <p style="color: var(--text-muted); text-align: center; font-style: italic; margin-top: 40px;">Наразі книг немає в каталозі.</p>
        <?php endif; ?>
    </main>
</div>