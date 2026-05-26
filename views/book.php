<?php
/** @var string $title */
/** @var array $book */
?>
<div class="book-detail-container">
    <div class="book-detail-aside">
        <?php if (!empty($book['cover_image'])): ?>
            <img src="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/public/uploads/<?php echo $book['cover_image']; ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="book-detail-cover">
        <?php else: ?>
            <div class="book-detail-placeholder">📚 Обкладинка відсутня</div>
        <?php endif; ?>
    </div>
    <div class="book-detail-main">
        <h2><?php echo htmlspecialchars($book['title']); ?></h2>
        <p><strong>Автор(и):</strong> <?php echo htmlspecialchars($book['authors_list'] ?: 'Не вказано'); ?></p>
        <p><strong>Жанр(и):</strong> <?php echo htmlspecialchars($book['genres_list'] ?: 'Не вказано'); ?></p>
        <p><strong>Видавництво:</strong> <?php echo htmlspecialchars($book['publisher_name']); ?></p>
        <p><strong>Рік видання:</strong> <?php echo htmlspecialchars($book['publication_year']); ?></p>
        <p><strong>ISBN:</strong> <?php echo htmlspecialchars($book['isbn']); ?></p>
        <p><strong>Кількість на складі:</strong> <?php echo htmlspecialchars($book['stock_quantity']); ?> шт.</p>

        <?php if (!empty($book['description'])): ?>
            <div style="margin: 25px 0; padding: 20px; background-color: rgba(255, 255, 255, 0.02); border-left: 4px solid var(--neon-glow); border-radius: 6px;">
                <h4 style="margin-top: 0; margin-bottom: 12px; color: var(--neon-glow); font-size: 16px;">Анотація:</h4>
                <p style="line-height: 1.7; color: var(--text-main); margin-bottom: 0; font-size: 15px;">
                    <?php echo nl2br(htmlspecialchars($book['description'])); ?>
                </p>
            </div>
        <?php endif; ?>

        <h3 style="margin-top: 25px;">Ціна: <span style="color: var(--success);"><?php echo htmlspecialchars($book['price']); ?> грн</span></h3>
        <br>
        <?php if ($book['stock_quantity'] > 0): ?>
            <button type="button" class="btn-add-to-cart" data-book-id="<?php echo $book['book_id']; ?>" style="width: auto; padding: 12px 30px;">
                Додати в кошик
            </button>
        <?php else: ?>
            <button type="button" disabled style="width: auto; padding: 12px 30px; background-color: var(--border); color: var(--text-muted); cursor: not-allowed; box-shadow: none;">
                Немає в наявності
            </button>
        <?php endif; ?>
        <p style="margin-top: 25px;">
            <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/" style="color: var(--accent-blue); text-decoration: none; font-weight: 600;">&larr; Назад до каталогу</a>
        </p>
    </div>
</div>