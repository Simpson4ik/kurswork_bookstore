<?php /** @var string $title */ ?>
<h2>Реєстрація нового користувача</h2>

<form action="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/register/store" method="POST" id="register-form">
    <?php if (!empty($error)): ?>
        <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid var(--danger); color: var(--danger); padding: 12px; border-radius: 8px; margin-bottom: 18px; font-weight: 600; font-size: 14px; text-align: center;">
            ✕ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div>
        <label for="first_name">Ім'я:</label>
        <input type="text" id="first_name" name="first_name" required>
    </div>

    <div>
        <label for="last_name">Прізвище:</label>
        <input type="text" id="last_name" name="last_name" required>
    </div>

    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <span id="email-error" style="color: var(--danger); font-size: 13px; display: block; margin-top: -12px; margin-bottom: 12px; font-weight: 600;"></span>
    </div>

    <div>
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>
    </div>

    <button type="submit" id="btn-register">Зареєструватися</button>
</form>

<p>Вже маєте акаунт? <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/login">Увійти тут</a></p>