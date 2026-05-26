<?php
/** @var string $title */
/** @var array $genres */
?>
<h2>Додати новий жанр</h2>
<?php if (!empty($error)): ?>
    <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid var(--danger); color: var(--danger); padding: 12px; border-radius: 8px; margin-bottom: 18px; font-weight: 600; font-size: 14px; text-align: center; max-width: 500px; margin-left: auto; margin-right: auto;">
        ✕ <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>
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
        <th>Дії</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($genres as $genre): ?>
        <tr id="genre-row-<?php echo htmlspecialchars($genre['genre_id']); ?>">
            <td><?php echo htmlspecialchars($genre['genre_id']); ?></td>
            <td><strong><?php echo htmlspecialchars($genre['genre_name']); ?></strong></td>
            <td>
                <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/admin/genre/delete/<?php echo htmlspecialchars($genre['genre_id']); ?>"
                   class="btn-delete-genre"
                   data-genre-id="<?php echo htmlspecialchars($genre['genre_id']); ?>"
                   style="color: var(--danger); text-decoration: none; font-weight: bold;">Видалити</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>