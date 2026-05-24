<?php
/** @var string $title */
/** @var array $orders */
?>
<div class="catalog-header-section">
    <div class="catalog-title-group">
        <h2>📦 <?php echo $title; ?></h2>
        <p>Відстежуйте статус поточних покупок та переглядайте повний архів ваших міжгалактичних замовлень</p>
    </div>
</div>

<div style="max-width: 900px; margin: 0 auto; padding: 20px;">
    <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $order): ?>
            <?php
            $statusColor = '#38bdf8';
            if ($order['status'] === 'Підтверджено') $statusColor = '#fbbf24';
            elseif ($order['status'] === 'Відправлено') $statusColor = '#a78bfa';
            elseif ($order['status'] === 'Виконано') $statusColor = '#10b981';
            elseif ($order['status'] === 'Скасовано') $statusColor = '#ef4444';
            ?>
            <div style="background-color: var(--panel-galaxy); border: 1px solid var(--border); border-radius: var(--radius); padding: 25px; margin-bottom: 25px; box-shadow: var(--shadow);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 15px; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <span style="font-size: 18px; font-weight: bold; color: var(--text-main);">Замовлення #<?php echo $order['order_id']; ?></span>
                        <span style="color: var(--text-muted); font-size: 14px; margin-left: 15px;">
                            📆 <?php echo isset($order['order_date']) ? date('d.m.Y H:i', strtotime($order['order_date'])) : 'Не вказано'; ?>
                        </span>
                    </div>
                    <div>
                        <span style="display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: bold; text-transform: uppercase; border: 1px solid <?php echo $statusColor; ?>; color: <?php echo $statusColor; ?>; background-color: rgba(11, 15, 25, 0.6); box-shadow: 0 0 10px <?php echo $statusColor; ?>40;">
                            🚀 <?php echo htmlspecialchars($order['status']); ?>
                        </span>
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); text-align: left;">
                            <th style="padding: 8px 0; color: var(--text-muted); font-size: 14px;">Книга</th>
                            <th style="padding: 8px 0; color: var(--text-muted); font-size: 14px; text-align: center;">Кількість</th>
                            <th style="padding: 8px 0; color: var(--text-muted); font-size: 14px; text-align: right;">Ціна</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                            <tr style="border-bottom: 1px dotted rgba(255,255,255,0.05);">
                                <td style="padding: 10px 0; color: var(--text-main); font-weight: 500;"><?php echo htmlspecialchars($item['title']); ?></td>
                                <td style="padding: 10px 0; text-align: center; color: var(--accent-blue); font-weight: bold;"><?php echo $item['quantity']; ?> шт.</td>
                                <td style="padding: 10px 0; text-align: right; color: var(--text-main);"><?php echo $item['price']; ?> грн</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px; padding-top: 10px;">
                    <span style="color: var(--text-muted); font-size: 14px;">Спосіб оплати: При отриманні</span>
                    <span style="font-size: 16px; font-weight: bold; color: var(--text-main);">
                        Разом: <span style="color: var(--success); font-size: 18px;"><?php echo $order['total_amount']; ?> грн</span>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px; background-color: var(--panel-galaxy); border: 1px solid var(--border); border-radius: var(--radius);">
            <p style="color: var(--text-muted); font-style: italic; font-size: 16px; margin-bottom: 20px;">Ви ще не оформили жодного замовлення.</p>
            <a href="/coursework/" class="page-link" style="display: inline-block;">📚 Перейти до каталогу книг</a>
        </div>
    <?php endif; ?>
</div>