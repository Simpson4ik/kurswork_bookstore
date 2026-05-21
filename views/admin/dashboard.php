<?php
/** @var string $title */
/** @var array $books */
?>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Керування каталогом книг</h2>
        <a href="/coursework/admin/book/add" style="text-decoration: none;">
            <button type="button" style="margin: 0; width: auto; background-color: var(--success);">➕ Додати нову книгу</button>
        </a>
    </div>

<?php if (!empty($books)): ?>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
        <tr>
            <th>ID</th>
            <th>Назва книги</th>
            <th>Автор(и)</th>
            <th>Жанр(и)</th>
            <th>ISBN</th>
            <th>Ціна</th>
            <th>Кількість</th>
            <th>Видавництво</th>
            <th>Дії</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($books as $book): ?>
            <tr>
                <td><?php echo $book['book_id']; ?></td>
                <td><strong><?php echo htmlspecialchars($book['title']); ?></strong></td>
                <td style="color: purple;"><?php echo htmlspecialchars($book['authors_list'] ?: '—'); ?></td>
                <td style="color: orange;"><?php echo htmlspecialchars($book['genres_list'] ?: '—'); ?></td>
                <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                <td><?php echo htmlspecialchars($book['price']); ?> грн</td>
                <td><?php echo $book['stock_quantity']; ?> шт.</td>
                <td><?php echo htmlspecialchars($book['publisher_name']); ?></td>
                <td>
                    <a href="/coursework/admin/book/edit/<?php echo $book['book_id']; ?>" style="color: blue; margin-right: 15px;">Редагувати</a>
                    <a href="/coursework/admin/book/delete/<?php echo $book['book_id']; ?>"
                       style="color: red;"
                       onclick="return confirm('Ви впевнені?')">Видалити</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>У каталозі поки немає жодної книги.</p>
<?php endif; ?>