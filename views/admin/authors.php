<?php
/** @var string $title */
/** @var array $authors */
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
</head>
<body>
<header>
    <h1>Панель керування — Автори</h1>
    <nav><a href="/coursework/admin/dashboard">Назад до головної адмінки</a></nav>
</header>
<main>
    <h2>Додати нового автора</h2>
    <form action="/coursework/admin/authors/store" method="POST">
        <div>
            <label for="first_name">Ім'я автора:</label><br>
            <input type="text" id="first_name" name="first_name" placeholder="Напр. Всеволод" style="padding: 5px; width: 300px;" required>
        </div>
        <br>
        <div>
            <label for="last_name">Прізвище автора:</label><br>
            <input type="text" id="last_name" name="last_name" placeholder="Напр. Нестайко" style="padding: 5px; width: 300px;" required>
        </div>
        <br>
        <div>
            <label for="biography">Біографія (необов'язково):</label><br>
            <textarea id="biography" name="biography" placeholder="Коротка інформація про автора..." style="padding: 5px; width: 304px; height: 70px;"></textarea>
        </div>
        <br>
        <button type="submit">Зберегти автора</button>
    </form>

    <br><hr><br>

    <h2>Наявні автори в базі</h2>
    <table border="1" cellpadding="8" cellspacing="0" style="width: 70%; text-align: left;">
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
</main>
</body>
</html>