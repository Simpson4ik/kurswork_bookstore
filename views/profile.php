<?php
/** @var string $title */
/** @var array $user */
?>
<div class="catalog-header-section">
    <div class="catalog-title-group">
        <h2>🚀 Космічний Термінал Користувача</h2>
        <p>Керуйте своїми персональними даними та відстежуйте міжзоряні замовлення</p>
    </div>
</div>

<div class="catalog-layout">
    <aside class="catalog-sidebar">
        <h3>Nav-Панель</h3>
        <div class="filter-section">
            <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/profile" class="page-link active" style="display: block; text-align: center; margin-bottom: 10px;">👤 Мій Профіль</a>
            <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/orders" class="page-link" style="display: block; text-align: center;">📦 Мої Замовлення</a>
        </div>
    </aside>

    <main class="catalog-main">
        <form id="profile-update-form" style="max-width: 100%; margin: 0;">
            <h3 style="color: var(--neon-glow); margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">Персональна інформація</h3>

            <div id="profile-status-message" style="margin-bottom: 15px; font-weight: 600; font-size: 14px;"></div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label for="first_name">Ім'я</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>
                <div>
                    <label for="last_name">Прізвище</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>
            </div>

            <label for="phone">Номер телефону</label>
            <input type="text" id="phone" name="phone" placeholder="+380XXXXXXXXX" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">

            <label for="email">E-mail адреса</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <button type="submit" id="btn-save-profile">Зберегти зміни терміналу</button>
        </form>
    </main>
</div>