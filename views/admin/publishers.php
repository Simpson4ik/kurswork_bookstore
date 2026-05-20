<?php
/** @var string $title */
/** @var array $publishers */
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
</head>
<body>
<header>
    <h1>Панель керування — Видавництва</h1>
    <nav>
        <a href="/coursework/admin/dashboard">Назад до головної адмінки</a>
    </nav>
</header>
<main>
    <h2>Додати нове видавництво</h2>
    <form action="/coursework/admin/publishers/store" method="POST">
        <input type="text" name="publisher_name" placeholder="Назва видавництва (напр. Наш Формат)" style="padding: 5px; width: 300px;" required>
        <button type="submit">Зберегти</button>
    </form>

    <br><hr><br>

    <h2>Наявні видавництва в базі</h2>
    <table border="1" cellpadding="8" cellspacing="0" style="width: 50%; text-align: left;">
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
</main>
</body>
</html>