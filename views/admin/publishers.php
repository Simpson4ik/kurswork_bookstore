<?php
/** @var string $title */
/** @var array $publishers */
?>
<h2>Додати нове видавництво</h2>
<form action="/coursework/admin/publishers/store" method="POST" style="max-width: 500px; margin: 0 auto; margin-bottom: 30px;">
    <label for="publisher_name">Назва видавництва:</label>
    <input type="text" id="publisher_name" name="publisher_name" placeholder="Напр. Наш Формат" required>
    <button type="submit">Зберегти</button>
</form>

<h2>Наявні видавництва в базі</h2>
<table border="1" cellpadding="8" cellspacing="0" style="max-width: 600px;">
    <thead>
    <tr>
        <th>ID</th>
        <th>Назва видавництва</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($publishers as $pub): ?>
        <tr>
            <td><?php echo $pub['publisher_id']; ?></td>
            <td><strong><?php echo htmlspecialchars($pub['publisher_name']); ?></strong></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>