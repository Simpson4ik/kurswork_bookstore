<?php /** @var string $content */ ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <script>window.BASE_PATH = "<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>";</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Панель адміністратора'); ?></title>
    <link rel="stylesheet" href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/public/css/style.css">
</head>
<body>
<header>
    <h1>⚙️ Панель керування магазином</h1>
    <nav>
        <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/">На головну сайту</a>
        <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/admin/dashboard">📊 Книги</a>
        <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/admin/orders">📦 Замовлення</a>
        <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/admin/publishers">🏢 Видавництва</a>
        <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/admin/authors">✍️ Автори</a>
        <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/admin/genres">🎭 Жанри</a>
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
<script src="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/public/js/app.js"></script>
</body>
</html>