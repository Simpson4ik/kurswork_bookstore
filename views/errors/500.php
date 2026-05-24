<?php

$displayTrace = $displayTrace ?? false;
$errorMessage = $errorMessage ?? '';
$errorTrace = $errorTrace ?? '';

?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>500 Внутрішня помилка сервера</title>
</head>
<body style="background:#0b0f19; color:#f8fafc; font-family:system-ui, sans-serif; text-align:center; padding-top:100px;">
<h1 style="color:#ef4444; font-size:64px; margin-bottom:10px;">500</h1>
<h2 style="font-size:24px; margin-bottom:15px;">Внутрішня помилка сервера</h2>
<p style="color:#94a3b8; max-width:500px; margin:0 auto 30px auto; line-height:1.6;">Сталася непередбачувана аномалія в системі розрахунків. Наші інженери вже усувають несправність.</p>
<a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/" style="display:inline-block; background:#2563eb; color:#fff; text-decoration:none; padding:12px 24px; font-weight:600; border-radius:8px;">&larr; Повернутися на головну</a>

<?php if ($displayTrace === true): ?>
    <pre style="text-align:left; max-width:800px; margin:40px auto; background:#161f33; padding:20px; border-radius:8px; color:#ef4444; overflow-x:auto; font-family:monospace; line-height:1.5; border:1px solid rgba(239, 68, 68, 0.2);"><?php echo htmlspecialchars($errorMessage) . "\n\n" . htmlspecialchars($errorTrace); ?></pre>
<?php endif; ?>
</body>
</html>