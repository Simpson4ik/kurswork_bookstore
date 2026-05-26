<?php
/** @var string $title */
/** @var array $stats */
/** @var array $books */
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2>Керування каталогом книг</h2>
    <div style="display: flex; gap: 15px;">
        <button type="button" id="btn-toggle-stats" style="margin: 0; width: auto; background-color: var(--accent-blue); transition: all 0.3s;">
            📊 Показати статистику
        </button>
        <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/admin/book/add" style="text-decoration: none;">
            <button type="button" style="margin: 0; width: auto; background-color: var(--success);">➕ Додати нову книгу</button>
        </a>
    </div>
</div>

<div id="stats-container" style="display: none; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px; animation: fadeIn 0.4s ease;">

    <div style="background: var(--panel-galaxy); border: 1px solid var(--border); padding: 20px; border-radius: var(--radius); box-shadow: var(--shadow); border-left: 4px solid var(--neon-glow);">
        <h3 style="margin: 0; color: var(--text-muted); font-size: 14px; text-transform: uppercase;">Всього книг</h3>
        <p style="margin: 10px 0 0 0; font-size: 28px; font-weight: bold; color: var(--text-main);"><?php echo $stats['total_books']; ?></p>
    </div>

    <div style="background: var(--panel-galaxy); border: 1px solid var(--border); padding: 20px; border-radius: var(--radius); box-shadow: var(--shadow); border-left: 4px solid #a78bfa;">
        <h3 style="margin: 0; color: var(--text-muted); font-size: 14px; text-transform: uppercase;">Замовлень</h3>
        <p style="margin: 10px 0 0 0; font-size: 28px; font-weight: bold; color: var(--text-main);"><?php echo $stats['total_orders']; ?></p>
    </div>

    <div style="background: var(--panel-galaxy); border: 1px solid var(--border); padding: 20px; border-radius: var(--radius); box-shadow: var(--shadow); border-left: 4px solid var(--success);">
        <h3 style="margin: 0; color: var(--text-muted); font-size: 14px; text-transform: uppercase;">Загальний дохід</h3>
        <p style="margin: 10px 0 0 0; font-size: 28px; font-weight: bold; color: var(--success);"><?php echo number_format($stats['total_revenue'], 2, '.', ' '); ?> ₴</p>
    </div>

    <div style="background: var(--panel-galaxy); border: 1px solid var(--border); padding: 20px; border-radius: var(--radius); box-shadow: var(--shadow); border-left: 4px solid #fbbf24;">
        <h3 style="margin: 0; color: var(--text-muted); font-size: 14px; text-transform: uppercase;">Клієнтів</h3>
        <p style="margin: 10px 0 0 0; font-size: 28px; font-weight: bold; color: var(--text-main);"><?php echo $stats['total_customers']; ?></p>
    </div>

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
            <tr id="book-row-<?php echo htmlspecialchars($book['book_id']); ?>">
                <td><?php echo htmlspecialchars($book['book_id']); ?></td>
                <td><strong><?php echo htmlspecialchars($book['title']); ?></strong></td>
                <td><?php echo htmlspecialchars($book['authors_list'] ?: '—'); ?></td>
                <td><?php echo htmlspecialchars($book['genres_list'] ?: '—'); ?></td>
                <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                <td><?php echo htmlspecialchars($book['price']); ?> грн</td>
                <td><?php echo htmlspecialchars($book['stock_quantity']); ?> шт.</td>
                <td><?php echo htmlspecialchars($book['publisher_name']); ?></td>
                <td>
                    <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/admin/book/edit/<?php echo htmlspecialchars($book['book_id']); ?>" style="color: var(--neon-glow); margin-right: 15px; text-decoration: none; font-weight: bold;">Редагувати</a>
                    <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/admin/book/delete/<?php echo htmlspecialchars($book['book_id']); ?>"
                       class="btn-delete-book"
                       data-book-id="<?php echo htmlspecialchars($book['book_id']); ?>"
                       style="color: var(--danger); text-decoration: none; font-weight: bold;">Видалити</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>У каталозі поки немає жодної книги.</p>
<?php endif; ?>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<script>
    document.getElementById('btn-toggle-stats').addEventListener('click', function() {
        const statsContainer = document.getElementById('stats-container');

        if (statsContainer.style.display === 'none' || statsContainer.style.display === '') {

            statsContainer.style.display = 'grid';
            this.style.backgroundColor = '#1e293b';
            this.style.border = '1px solid var(--border)';
            this.innerHTML = '👁️ Сховати статистику';
        } else {
            statsContainer.style.display = 'none';
            this.style.backgroundColor = 'var(--accent-blue)';
            this.style.border = 'none';
            this.innerHTML = '📊 Показати статистику';
        }
    });
</script>