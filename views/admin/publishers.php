<?php
/** @var string $title */
/** @var array $publishers */
?>
<h2>Додати нове видавництво</h2>
<form action="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/admin/publishers/store" method="POST" style="max-width: 500px; margin: 0 auto; margin-bottom: 30px;">
    <label for="publisher_name">Назва видавництва:</label>
    <input type="text" id="publisher_name" name="publisher_name" placeholder="Напр. Наш Формат" required>
    <button type="submit">Зберегти</button>
</form>

<h2>Наявні видавництва в базі</h2>
<?php if (!empty($error)): ?>
    <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid var(--danger); color: var(--danger); padding: 12px; border-radius: 8px; margin-bottom: 18px; font-weight: 600; font-size: 14px; text-align: center; max-width: 500px; margin-left: auto; margin-right: auto;">
        ✕ <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>
<table border="1" cellpadding="8" cellspacing="0" style="max-width: 600px;">
    <thead>
    <tr>
        <th>ID</th>
        <th>Назва видавництва</th>
        <th>Дії</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($publishers as $pub): ?>
        <tr id="publisher-row-<?php echo htmlspecialchars($pub['publisher_id']); ?>">
            <td><?php echo htmlspecialchars($pub['publisher_id']); ?></td>
            <td><strong><?php echo htmlspecialchars($pub['publisher_name']); ?></strong></td>
            <td>
                <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/admin/publisher/delete/<?php echo htmlspecialchars($pub['publisher_id']); ?>"
                   class="btn-delete-publisher"
                   data-publisher-id="<?php echo htmlspecialchars($pub['publisher_id']); ?>"
                   style="color: var(--danger); text-decoration: none; font-weight: bold;">Видалити</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>