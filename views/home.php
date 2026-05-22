<?php
/** @var string $title */
/** @var array $books */
/** @var int $currentPage */
/** @var int $totalPages */
?>
    <h2>Каталог книг</h2>

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