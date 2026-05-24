<?php
/** @var string $title */
/** @var array $genres */
?>
<h2>Додати новий жанр</h2>
<form action="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/admin/genres/store" method="POST" style="max-width: 500px; margin: 0 auto; margin-bottom: 30px;">
    <label for="genre_name">Назва жанру:</label>
    <input type="text" id="genre_name" name="genre_name" placeholder="Напр. Пригоди" required>
    <button type="submit">Зберегти</button>
</form>

<h2>Наявні жанри</h2>
<table border="1" cellpadding="8" cellspacing="0" style="max-width: 600px;">
    <thead>
    <tr>
        <th>ID</th>
        <th>Назва жанру</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($genres as $genre): ?>
        <tr>
            <td><?php echo htmlspecialchars($genre['genre_id']); ?></td>
            <td><strong><?php echo htmlspecialchars($genre['genre_name']); ?></strong></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>