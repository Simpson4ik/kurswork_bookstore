<?php
/** @var string $title */
/** @var array $genres */
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
</head>
<body>
<header>
    <h1>Панель керування — Жанри</h1>
    <nav><a href="/coursework/admin/dashboard">Назад до головної адмінки</a></nav>
</header>
<main>
    <h2>Додати новий жанр</h2>
    <form action="/coursework/admin/genres/store" method="POST">
        <input type="text" name="genre_name" placeholder="Назва жанру (напр. Пригоди)" style="padding: 5px; width: 300px;" required>
        <button type="submit">Зберегти</button>
    </form>
    <br><hr><br>
    <h2>Наявні жанри</h2>
    <table border="1" cellpadding="8" cellspacing="0" style="width: 50%;">
        <thead><tr><th>ID</th><th>Назва жанру</th></tr></thead>
        <tbody>
        <?php foreach ($genres as $genre): ?>
            <tr>
                <td><?php echo $genre['genre_id']; ?></td>
                <td><strong><?php echo htmlspecialchars($genre['genre_name']); ?></strong></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>
</body>
</html>