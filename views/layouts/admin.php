<?php /** @var string $content */ ?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Панель адміністратора'; ?></title>
    <link rel="stylesheet" href="/coursework/public/css/style.css?v=2">
</head>
<body>
<header>
    <h1>⚙️ Панель керування магазином</h1>
    <nav>
        <a href="/coursework/">На головну сайту</a>
        <a href="/coursework/admin/dashboard">📊 Книги (CRUD)</a>
        <a href="/coursework/admin/publishers">🏢 Видавництва</a>
        <a href="/coursework/admin/authors">✍️ Автори</a>
        <a href="/coursework/admin/genres">🎭 Жанри</a>
    </nav>
</header>

<main>
    <?php echo $content; ?>
</main>

<footer>
    <p style="text-align: center; color: var(--text-muted); font-size: 14px; margin-top: 40px;">
        Режим адміністрування &copy; <?php echo date('Y'); ?> Bookstore CRM.
    </p>
</footer>
</body>
</html>