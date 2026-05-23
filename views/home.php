<?php
/** @var string $title */
/** @var array $books */
/** @var array $genres */
/** @var int $currentPage */
/** @var int $totalPages */
?>
<h2>Каталог книг</h2>

<div class="search-catalog-wrapper" style="margin: 0 auto 35px auto; max-width: 650px; padding: 0 15px;">
    <div style="position: relative;">
        <input type="text"
               id="catalog-live-search"
               placeholder="Швидкий пошук книги, автора або жанру..."
               autocomplete="off"
               style="width: 100% !important;
                      padding: 15px 20px 15px 50px !important;
                      border-radius: 30px;
                      border: 2px solid var(--border);
                      background: var(--panel-galaxy) !important;
                      color: var(--text-main) !important;
                      font-size: 15px;
                      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4), 0 0 15px rgba(56, 189, 248, 0.05);
                      transition: all 0.3s ease;
                      margin-bottom: 0 !important;">
        <span style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); font-size: 18px; color: var(--neon-glow); opacity: 0.8; pointer-events: none;">
            🔍
        </span>
    </div>
</div>

<div class="catalog-layout">
    <aside class="catalog-sidebar">
        <h3 style="color: var(--neon-glow); border-bottom: 1px solid var(--border); padding-bottom: 10px; margin-bottom: 20px; font-size: 18px;">
            🎛️ Фільтри каталогу
        </h3>

        <div class="filter-section" style="margin-bottom: 25px;">
            <h4 style="margin-bottom: 12px; font-size: 14px; text-transform: uppercase; color: var(--text-muted);">
                Ціна (грн)
            </h4>
            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="number" id="filter-min-price" placeholder="Від" min="0" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border); background: rgba(15, 23, 42, 0.5); color: #fff;">
                <span style="color: var(--text-muted);">&mdash;</span>
                <input type="number" id="filter-max-price" placeholder="До" min="0" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border); background: rgba(15, 23, 42, 0.5); color: #fff;">
            </div>
        </div>

        <div class="filter-section" style="margin-bottom: 25px;">
            <h4 style="margin-bottom: 12px; font-size: 14px; text-transform: uppercase; color: var(--text-muted);">
                Жанри літератури
            </h4>
            <div style="max-height: 180px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px; padding-right: 5px;">
                <?php foreach ($genres as $genre): ?>
                    <label style="display: flex; align-items: center; gap: 10px; font-weight: normal; cursor: pointer; font-size: 14px;">
                        <input type="checkbox" class="filter-genre-checkbox" value="<?php echo $genre['genre_id']; ?>" style="cursor: pointer; width: 16px; height: 16px;">
                        <?php echo htmlspecialchars($genre['genre_name']); ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="filter-section" style="border-top: 1px solid var(--border); padding-top: 15px;">
            <label style="display: flex; align-items: center; gap: 10px; font-weight: normal; cursor: pointer; font-size: 14px;">
                <input type="checkbox" id="filter-in-stock" style="cursor: pointer; width: 16px; height: 16px;">
                Тільки в наявності (в наявності)
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
                                <img src="/coursework/public/uploads/<?php echo $book['cover_image']; ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="book-cover">
                            <?php else: ?>
                                <div class="book-cover-placeholder">📚 Обкладинка відсутня</div>
                            <?php endif; ?>
                        </div>
                        <h3>
                            <a href="/coursework/book/<?php echo $book['book_id']; ?>">
                                <?php echo htmlspecialchars($book['title']); ?>
                            </a>
                        </h3>
                        <p><strong>Автор(и):</strong> <?php echo htmlspecialchars($book['authors_list'] ?: 'Не вказано'); ?></p>
                        <p><strong>Жанр(и):</strong> <?php echo htmlspecialchars($book['genres_list'] ?: 'Не вказано'); ?></p>
                        <p>Рік видання: <?php echo htmlspecialchars($book['publication_year']); ?></p>
                        <p>Ціна: <?php echo htmlspecialchars($book['price']); ?> грн</p>
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
                        <a href="/coursework/?page=<?php echo $currentPage - 1; ?>" class="page-link">&larr; Назад</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="/coursework/?page=<?php echo $i; ?>" class="page-link <?php echo $i === $currentPage ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="/coursework/?page=<?php echo $currentPage + 1; ?>" class="page-link">Вперед &rarr;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <p>Наразі книг немає в каталозі.</p>
        <?php endif; ?>
    </main>
</div>