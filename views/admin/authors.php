<?php
/** @var string $title */
/** @var array $authors */
?>
<h2>Додати нового автора</h2>
<form action="/coursework/admin/authors/store" method="POST" style="max-width: 500px; margin: 0 auto; margin-bottom: 30px;">
    <div>
        <label for="first_name">Ім'я автора:</label>
        <input type="text" id="first_name" name="first_name" placeholder="Напр. Всеволод" required>
    </div>
    <div>
        <label for="last_name">Прізвище автора:</label>
        <input type="text" id="last_name" name="last_name" placeholder="Напр. Нестайко" required>
    </div>
    <div>
        <label for="biography">Біографія (необов'язково):</label>
        <textarea id="biography" name="biography" placeholder="Коротка інформація про автора..." style="height: 80px;"></textarea>
    </div>
    <button type="submit">Зберегти автора</button>
</form>

<h2>Наявні автори в базі</h2>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Автор (Прізвище та Ім'я)</th>
        <th>Біографія</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($authors as $author): ?>
        <tr>
            <td><?php echo $author['author_id']; ?></td>
            <td><strong><?php echo htmlspecialchars($author['last_name'] . ' ' . $author['first_name']); ?></strong></td>
            <td><?php echo htmlspecialchars($author['biography'] ?? '—'); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>