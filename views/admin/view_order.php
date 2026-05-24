<?php
/** @var string $title */
/** @var array $order */
?>
<div class="catalog-header-section">
    <div class="catalog-title-group">
        <h2>🔍 Деталі замовлення #<?php echo htmlspecialchars($order['order_id']); ?></h2>
        <p>Повна інформація про покупця, склад товарів та фінансові підсумки транзакції</p>
    </div>
</div>

<div class="catalog-layout">
    <aside class="catalog-sidebar">
        <h3>👤 Дані покупця</h3>
        <div class="filter-section">
            <p><strong>Ім'я:</strong> <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
            <p><strong>Телефон:</strong> <?php echo htmlspecialchars($order['phone'] ?: 'Не вказано'); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
        </div>
        <div class="filter-section" style="border-top: 1px solid var(--border); padding-top: 15px;">
            <p><strong>Дата:</strong> <?php echo htmlspecialchars(date('d.m.Y H:i', strtotime($order['order_date']))); ?></p>
            <p><strong>Статус:</strong> <span style="color: var(--neon-glow); font-weight: bold;"><?php echo htmlspecialchars($order['status']); ?></span></p>
        </div>
        <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/admin/orders" class="page-link" style="display: block; text-align: center; margin-top: 20px;">&larr; Назад до списку</a>
    </aside>

    <main class="catalog-main">
        <h3 style="color: var(--neon-glow); margin-top: 0; margin-bottom: 15px;">📚 Склад замовлення</h3>
        <table>
            <thead>
            <tr>
                <th>Назва книги</th>
                <th>Ціна за шт.</th>
                <th>Кількість</th>
                <th>Загальна вартість</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($order['items'] as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['title'] ?: 'Видалена книга'); ?></td>
                    <td><?php echo htmlspecialchars($item['price']); ?> грн</td>
                    <td style="color: var(--neon-glow); font-weight: bold;"><?php echo htmlspecialchars($item['quantity']); ?> шт.</td>
                    <td><?php echo htmlspecialchars($item['price'] * $item['quantity']); ?> грн</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div style="text-align: right; margin-top: 20px; font-size: 20px; font-weight: bold;">
            Разом до сплати: <span style="color: var(--success);"><?php echo htmlspecialchars($order['total_amount']); ?> грн</span>
        </div>
    </main>
</div>