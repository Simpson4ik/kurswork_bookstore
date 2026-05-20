<?php
/** @var string $title */
/** @var array $book */
?>
<article>
    <h2><?php echo htmlspecialchars($book['title']); ?></h2>
    <p><strong>ISBN:</strong> <?php echo htmlspecialchars($book['isbn']); ?></p>
    <p><strong>Рік видання:</strong> <?php echo htmlspecialchars($book['publication_year']); ?></p>
    <p><strong>Видавництво:</strong> <?php echo htmlspecialchars($book['publisher_name']); ?></p>
    <p><strong>Ціна:</strong> <?php echo htmlspecialchars($book['price']); ?> грн</p>
    <p><strong>Кількість на складі:</strong> <?php echo htmlspecialchars($book['stock_quantity']); ?> шт.</p>
</article>
<p><a href="/coursework/">Повернутися до каталогу</a></p>