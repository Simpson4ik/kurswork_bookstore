<?php
/** @var string $title */
/** @var array $book */
?>
<div class="book-detail-container">
    <div class="book-detail-aside">
        <?php if (!empty($book['cover_image'])): ?>
            <img src="/coursework/public/uploads/<?php echo $book['cover_image']; ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="book-detail-cover">
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
        <h3>Ціна: <?php echo htmlspecialchars($book['price']); ?> грн</h3>
        <br>
        <button type="button" class="btn-add-to-cart" data-book-id="<?php echo $book['book_id']; ?>" style="width: auto; padding: 12px 30px;">
            Додати в кошик
        </button>
        <p style="margin-top: 20px;"><a href="/coursework/">&larr; Назад до каталогу</a></p>
    </div>
</div>