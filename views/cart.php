<?php
/** @var string $title */
/** @var array $cartItems */
/** @var float $totalPrice */
/** @var array|null $user */
?>
<h2><?php echo $title; ?></h2>

<div id="cart-container">
    <?php if (!empty($cartItems)): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
            <tr>
                <th>Назва книги</th>
                <th>Ціна</th>
                <th>Кількість</th>
                <th>Сума</th>
                <th>Дія</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($cartItems as $item): ?>
                <tr id="cart-row-<?php echo $item['book_id']; ?>">
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                    <td><?php echo htmlspecialchars($item['price']); ?> грн</td>
                    <td>
                        <input type="number"
                               class="cart-qty-input"
                               data-book-id="<?php echo $item['book_id']; ?>"
                               value="<?php echo $item['quantity']; ?>"
                               min="1"
                               max="<?php echo $item['stock_quantity']; ?>"
                               style="width: 60px;">
                        <small style="color: gray; display: block;">(доступно: <?php echo $item['stock_quantity']; ?>)</small>
                    </td>
                    <td id="subtotal-<?php echo $item['book_id']; ?>"><?php echo $item['subtotal']; ?> грн</td>
                    <td>
                        <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/cart/remove/<?php echo $item['book_id']; ?>" class="btn-remove-item" data-book-id="<?php echo $item['book_id']; ?>" style="color: red; text-decoration: none;">Видалити</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Загальна вартість: <span id="grand-total"><?php echo $totalPrice; ?> грн</span></h3>

        <?php if (isset($_SESSION['user'])): ?>
            <form action="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/cart/checkout" method="POST" style="max-width: 100%; margin-top: 30px; padding: 25px;">
                <h3 style="color: var(--neon-glow); margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">📋 Дані для відправки замовлення</h3>

                <?php if (!empty($error)): ?>
                    <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid var(--danger); color: var(--danger); padding: 12px; border-radius: 8px; margin-bottom: 18px; font-weight: 600; font-size: 14px; text-align: center;">
                        ✕ <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="first_name">Ім'я</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label for="last_name">Прізвище</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                    </div>
                </div>

                <div>
                    <label for="phone">Номер телефону</label>
                    <input type="text" id="phone" name="phone" placeholder="+380XXXXXXXXX" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                </div>

                <button type="submit" style="background: var(--success); color: white; padding: 12px 25px; font-size: 16px; border: none; cursor: pointer; width: auto; margin-top: 15px;">
                    🚀 Підтвердити та оформити замовлення
                </button>
            </form>
        <?php else: ?>
            <div style="background-color: var(--panel-galaxy); border: 1px solid var(--border); padding: 20px; border-radius: var(--radius); margin-top: 30px; text-align: center;">
                <p style="color: var(--text-muted); margin-bottom: 15px;">Для оформлення замовлення необхідно увійти до свого облікового запису.</p>
                <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/login" class="page-link" style="display: inline-block; margin-right: 10px;">🔑 Вхід</a>
                <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/register" class="page-link" style="display: inline-block;">📝 Реєстрація</a>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <p style="color: var(--text-muted);">Ваш кошик порожній.</p>
    <?php endif; ?>
</div>