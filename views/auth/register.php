<?php /** @var string $title */ ?>
<h2>Реєстрація нового користувача</h2>
<form action="/coursework/register/store" method="POST">
    <div>
        <label for="first_name">Ім'я:</label>
        <input type="text" id="first_name" name="first_name" required>
    </div>
    <br>
    <div>
        <label for="last_name">Прізвище:</label>
        <input type="text" id="last_name" name="last_name" required>
    </div>
    <br>
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
    <button type="submit">Зареєструватися</button>
</form>
<p>Вже маєте акаунт? <a href="/coursework/login">Увійти тут</a></p>