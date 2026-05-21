<?php
/** @var string $title */
/** @var array $cartItems */
/** @var float $totalPrice */
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
                        <a href="/coursework/cart/remove/<?php echo $item['book_id']; ?>" style="color: red;">Видалити</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Загальна вартість: <span id="grand-total"><?php echo $totalPrice; ?> грн</span></h3>

        <a href="/coursework/cart/checkout">
            <button type="button" style="background: var(--success); color: white; padding: 10px 20px; font-size: 16px; border: none; cursor: pointer; width: auto; margin-top: 10px;">
                🚀 Оформити замовлення
            </button>
        </a>
    <?php else: ?>
        <p>Ваш кошик порожній.</p>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qtyInputs = document.querySelectorAll('.cart-qty-input');

        qtyInputs.forEach(input => {
            input.addEventListener('change', function() {
                const bookId = this.getAttribute('data-book-id');
                const newQty = parseInt(this.value);

                fetch('/coursework/cart/update-ajax', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        book_id: bookId,
                        quantity: newQty
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            input.value = data.quantity;
                            if (data.quantity === 0) {
                                const row = document.getElementById(`cart-row-${bookId}`);
                                if (row) row.remove();
                            } else {
                                document.getElementById(`subtotal-${bookId}`).innerText = data.subtotal;
                            }

                            document.getElementById('grand-total').innerText = data.total_price;

                            if (data.cart_empty) {
                                document.getElementById('cart-container').innerHTML = '<p>Ваш кошик порожній.</p>';
                            }
                        } else {
                            alert('Помилка оновлення: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Помилка мережі:', error);
                    });
            });
        });
    });
</script>