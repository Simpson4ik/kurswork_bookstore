<?php /** @var string $content */ ?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <script>window.BASE_PATH = "<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>";</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Bookstore X'); ?></title>
    <link rel="stylesheet" href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/public/css/style.css">
</head>
<body>
<header>
    <h1><a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/" style="text-decoration: none; color: inherit;">🌌 Bookstore X</a></h1>
    <nav>
        <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/">📋 Каталог</a>
        <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/cart">🛒 Кошик</a>
        <?php if (isset($_SESSION['user'])): ?>
            <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/orders">📦 Замовлення</a>
            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/admin/dashboard" class="admin-link">👨‍💻 Admin-панель</a>
            <?php endif; ?>
            <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/profile" class="profile-link">👤 Кабінет</a>
            <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/logout" class="logout-link">🚪 Вихід</a>
        <?php else: ?>
            <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/login">🔑 Вхід</a>
            <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/register">📝 Реєстрація</a>
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
<script src="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/public/js/app.js"></script>
</body>
</html>