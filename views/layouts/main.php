<?php /** @var string $content */ ?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Книжковий інтернет-магазин'; ?></title>
    <link rel="stylesheet" href="/coursework/public/css/style.css?v=3">

</head>
<body>
<header>
    <h1>📚 Книжковий інтернет-магазин</h1>
    <nav>
        <a href="/coursework/">🏠 Головна</a>
        <a href="/coursework/cart">🛒 Мій Кошик</a>

        <?php if (isset($_SESSION['user'])): ?>
            <span class="user-welcome">Вітаємо, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</span>

            <a href="/coursework/orders">📜 Мої замовлення</a>

            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <a href="/coursework/admin/dashboard" class="admin-link">👨‍💻 Адмінка</a>
            <?php endif; ?>

            <a href="/coursework/logout" class="logout-link">Вихід</a>
        <?php else: ?>
            <a href="/coursework/login">Вхід</a>
            <a href="/coursework/register">Реєстрація</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <?php echo $content; ?>
</main>

<footer>
    <p style="text-align: center; color: var(--text-muted); font-size: 14px;">
        &copy; <?php echo date('Y'); ?> Книжковий інтернет-магазин. Курсова робота.
    </p>
</footer>
<script src="/coursework/public/js/app.js?v=1"></script>
</body>
</html>