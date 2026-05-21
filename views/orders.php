<?php
/** @var string $title */
/** @var array $orders */
?>
<h2>📦 Історія моїх замовлень</h2>

<?php if (!empty($orders)): ?>
    <?php foreach ($orders as $order): ?>
        <div class="order-card">
            <div class="order-header">
                <span style="font-weight: bold; color: var(--neon-glow);">Замовлення #<?php echo $order['order_id']; ?></span>
                <span style="color: var(--text-muted); font-size: 14px;">
                    📅 <?php echo !empty($order['order_date']) ? date('d.m.Y H:i', strtotime($order['order_date'])) : 'Щойно оформлено'; ?>
                </span>
            </div>

            <table border="0" cellpadding="5" cellspacing="0" style="margin: 10px 0; box-shadow: none; border: none;">
                <thead>
                <tr style="background: none;">
                    <th style="background: none; padding: 5px; color: var(--text-muted);">Книга</th>
                    <th style="background: none; padding: 5px; color: var(--text-muted); text-align: center;">Кількість</th>
                    <th style="background: none; padding: 5px; color: var(--text-muted); text-align: right;">Ціна</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td style="padding: 6px 5px;"><?php echo htmlspecialchars($item['title']); ?></td>
                        <td style="padding: 6px 5px; text-align: center; color: var(--neon-glow);"><?php echo $item['quantity']; ?> шт.</td>
                        <td style="padding: 6px 5px; text-align: right;"><?php echo $item['price']; ?> грн</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <div style="text-align: right; margin-top: 10px; font-weight: bold; font-size: 16px;">
                Загальна сума: <span style="color: var(--success);"><?php echo $order['total_amount']; ?> грн</span>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p style="color: var(--text-muted);">Ви ще нічого не замовляли в нашому магазині.</p>
<?php endif; ?>

<p><a href="/coursework/">&larr; Повернутися до каталогу книг</a></p>