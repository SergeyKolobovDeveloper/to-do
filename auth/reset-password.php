<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$token = $_GET['token'] ?? '';
$error = $_SESSION['reset_error'] ?? '';

unset($_SESSION['reset_error']);

$user = null;

if (!empty($token)) {
    $sql = 'SELECT * FROM `users` WHERE `reset_token` = :token';
    $data = $pdo->prepare($sql);
    $data->execute([':token' => $token]);

    $user = $data->fetch(PDO::FETCH_ASSOC);
}

require_once __DIR__ . '/../includes/header.php';
?>
<main class="container my-5 flex-grow-1 d-flex align-items-center justify-content-center">
    <div class="card shadow" style="width: 100%; max-width: 400px;">
        <div class="card-body p-4">
            <h2 class="mb-4 text-center">Встановлення нового пароля</h2>

            <?php if(!$user): ?>
                <div class="alert alert-danger text-center">
                    Посилання для скидання пароля недійсне або застаріло.
                </div>
                <div class="text-center mt-3">
                    <a href="password-recovery.php" class="btn btn-outline-success">Нове посилання на скидання пароля</a>
                </div>
            <?php else: ?>
                <?php if($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form action="../actions/reset-password-handler.php" method="post">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <div class="mb-3">
                        <label for="password" class="form-label">Новий пароль</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Мінімум 6 символів" required>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Підтвердження пароля</label>
                        <input type="password" id="password_confirm" name="password_confirm" class="form-control" placeholder="Повторіть новий пароль" required>
                    </div>

                    <input type="submit" class="btn btn-success w-100 mb-3" value="Зберегти новий пароль">
                </form>
            <?php endif; ?>
        </div>
    </div>
</main>