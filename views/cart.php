<?php
/** @var string $title */
/** @var array $cartItems */
/** @var float $totalPrice */
?>
    <h2><?php echo $title; ?></h2>

<?php if (!empty($cartItems)): ?>
    <form action="/coursework/cart/update" method="POST">
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
                <tr>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                    <td><?php echo htmlspecialchars($item['price']); ?> грн</td>
                    <td>
                        <input type="number"
                               name="quantities[<?php echo $item['book_id']; ?>]"
                               value="<?php echo $item['quantity']; ?>"
                               min="1"
                               max="<?php echo $item['stock_quantity']; ?>"
                               style="width: 60px;">
                        <small style="color: gray; display: block;">(доступно: <?php echo $item['stock_quantity']; ?>)</small>
                    </td>
                    <td><?php echo $item['subtotal']; ?> грн</td>
                    <td>
                        <a href="/coursework/cart/remove/<?php echo $item['book_id']; ?>" style="color: red;">Видалити</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <br>
        <button type="submit">Оновити кошик</button>
    </form>

    <h3>Загальна вартість: <?php echo $totalPrice; ?> грн</h3>
    <a href="/coursework/cart/checkout">
        <button type="button" style="background: green; color: white; padding: 10px 20px; font-size: 16px; border: none; cursor: pointer;">
            🚀 Оформити замовлення
        </button>
    </a>
<?php else: ?>
    <p>Ваш кошик порожній.</p>
<?php endif; ?>