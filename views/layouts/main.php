<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Книжковий інтернет-магазин'; ?></title>
</head>
<body>
<header>
    <h1>📚 Книжковий інтернет-магазин</h1>
    <nav>
        <a href="/coursework/">🏠 Головна</a> |
        <a href="/coursework/cart">🛒 Мій Кошик</a> |

        <?php if (isset($_SESSION['user'])): ?>
            <strong>Вітаємо, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</strong>

            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                | <a href="/coursework/admin/dashboard" style="color: blue; font-weight: bold;">👨‍💻 Адмінка</a>
            <?php endif; ?>

            | <a href="/coursework/logout">Вихід</a>
        <?php else: ?>
            <a href="/coursework/login">Вхід</a> |
            <a href="/coursework/register">Реєстрація</a>
        <?php endif; ?>
    </nav>
</header>
<hr>

<main>
    <?php echo $content; ?>
</main>

<hr>
<footer>
    <p style="text-align: center; color: gray;">
        &copy; <?php echo date('Y'); ?> Книжковий інтернет-магазин. Курсова робота.
    </p>
</footer>
</body>
</html>