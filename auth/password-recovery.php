<?php
session_start();

$error = $_SESSION['pass_recovery_error'] ?? '';
$success = $_SESSION['pass_recovery_success'] ?? '';
unset($_SESSION['pass_recovery_error'], $_SESSION['pass_recovery_success']);

require_once __DIR__ . '/../includes/header.php';
?>
<main class="container my-5 flex-grow-1 d-flex align-items-center justify-content-center">
    <div class="card shadow" style="width: 100%; max-width: 400px;">
        <div class="card-body p-4">
            <h2 class="mb-4 text-center">Відновлення пароля</h2>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <p class="text-muted text-center mb-4">Введіть ваш Email, і ми надішлемо вам посилання для скидання пароля.</p>

            <form action="../actions/password-recovery-handler.php" method="post">
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Ваш Email" required>
                </div>
                <input type="submit" class="btn btn-success w-100 mb-3" value="Надіслати посилання">
            </form>
            <div class="text-center">
                <a href="login.php" class="text-decoration-none link-success fw-semibold">Я згадав пароль! Повернутися до входу</a>
            </div>
        </div>
    </div>
</main>
<?php
require_once __DIR__ . '/../includes/footer.php'; 
?>