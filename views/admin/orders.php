<?php
/** @var string $title */
/** @var array $orders */
/** @var int $currentPage */
/** @var int $totalPages */
?>
<div class="catalog-header-section">
    <div class="catalog-title-group">
        <h2>📦 CRM: Управління замовленнями</h2>
        <p>Контроль статусів, перегляд складу замовлень та оперативна обробка купівель</p>
    </div>
</div>

<div class="catalog-main" style="max-width: 100%;">
    <?php if (!empty($orders)): ?>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Покупець</th>
                <th>Email</th>
                <th>Сума</th>
                <th>Дата оформлення</th>
                <th>Поточний статус</th>
                <th>Дії</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?php echo htmlspecialchars($order['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['email']); ?></td>
                    <td style="color: var(--success); font-weight: bold;"><?php echo htmlspecialchars($order['total_amount']); ?> грн</td>
                    <td><?php echo htmlspecialchars(date('d.m.Y H:i', strtotime($order['order_date']))); ?></td>
                    <td>
                        <select class="admin-status-select" data-order-id="<?php echo htmlspecialchars($order['order_id']); ?>" style="margin-bottom: 0; padding: 6px; border-radius: 6px; font-weight: bold; background-color: var(--bg-galaxy); color: var(--text-main); border: 1px solid var(--border);">
                            <option value="Нове" <?php echo $order['status'] === 'Нове' ? 'selected' : ''; ?> style="color: #38bdf8;">Нове</option>
                            <option value="Підтверджено" <?php echo $order['status'] === 'Підтверджено' ? 'selected' : ''; ?> style="color: #fbbf24;">Підтверджено</option>
                            <option value="Відправлено" <?php echo $order['status'] === 'Відправлено' ? 'selected' : ''; ?> style="color: #a78bfa;">Відправлено</option>
                            <option value="Виконано" <?php echo $order['status'] === 'Виконано' ? 'selected' : ''; ?> style="color: #10b981;">Виконано</option>
                            <option value="Скасовано" <?php echo $order['status'] === 'Скасовано' ? 'selected' : ''; ?> style="color: #ef4444;">Скасовано</option>
                        </select>
                    </td>
                    <td>
                        <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/admin/orders/view/<?php echo htmlspecialchars($order['order_id']); ?>" class="page-link" style="padding: 6px 12px; font-size: 13px;">🔍 Переглянути</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/admin/orders?page=<?php echo $currentPage - 1; ?>" class="page-link">&larr; Назад</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/admin/orders?page=<?php echo $i; ?>" class="page-link <?php echo $i === $currentPage ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/admin/orders?page=<?php echo $currentPage + 1; ?>" class="page-link">Вперед &rarr;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <p style="color: var(--text-muted); text-align: center; font-style: italic; margin-top: 40px;">Замовлень від користувачів поки немає.</p>
    <?php endif; ?>
</div>