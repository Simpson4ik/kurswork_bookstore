<?php /** @var string $title */ ?>
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
    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 18px;">
        <input type="checkbox" id="remember" name="remember" style="width: auto; margin: 0;">
        <label for="remember" style="margin: 0; font-weight: normal;">Запам'ятати мене</label>
    </div>
    <button type="submit">Увійти</button>
</form>
<p>Немає акаунту? <a href="/coursework/register">Зареєстроватися</a></p>