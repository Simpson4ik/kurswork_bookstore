<?php /** @var string $title */ ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
</head>
<body>
<main>
    <h2>Вхід у систему</h2>
    <form action="/coursework/login/authenticate" method="POST">
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <br>
        <div>
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <br>
        <button type="submit">Увійти</button>
    </form>
    <p>Немає акаунту? <a href="/coursework/register">Зареєструватися</a></p>
</main>
</body>
</html>