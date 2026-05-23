<?php /** @var string $content */ ?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Bookstore X'); ?></title>
    <link rel="stylesheet" href="/coursework/public/css/style.css?v=4">
</head>
<body>
<header>
    <h1><a href="/coursework/" style="text-decoration: none; color: inherit;">🌌 Bookstore X</a></h1>
    <nav>
        <a href="/coursework/">📋 Каталог</a>
        <a href="/coursework/cart">🛒 Кошик</a>
        <?php if (isset($_SESSION['user'])): ?>
            <a href="/coursework/orders">📦 Замовлення</a>
            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <a href="/coursework/admin/dashboard" class="admin-link">👨‍💻 Адмінка</a>
            <?php endif; ?>
            <a href="/coursework/profile" class="profile-link">👤 Кабінет</a>
            <a href="/coursework/logout" class="logout-link">🚪 Вихід</a>
        <?php else: ?>
            <a href="/coursework/login">🔑 Вхід</a>
            <a href="/coursework/register">📝 Реєстрація</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <?php echo $content; ?>
</main>

<footer>
    <p style="text-align: center; color: var(--text-muted); font-size: 14px;">
        &copy; <?php echo date('Y'); ?> Bookstore X. Усі права захищено міжгалактичним правом.
    </p>
</footer>
<script src="/coursework/public/js/app.js?v=2"></script>
</body>
</html>