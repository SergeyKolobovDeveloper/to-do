<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/app.php';
if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$title = "Мій профіль — DiloFlow";
require_once __DIR__ . '/../includes/header.php';

$user = $_SESSION['user'];

require_once __DIR__ . '/../config/db.php';

// Отримуємо актуальні дані про користувача з БД
$sql = 'SELECT `telegram_chat_id`, `email`, `login` FROM `users` WHERE `id` = :id';
$data = $pdo->prepare($sql);
$data->execute(['id' => $user['id']]);
$dbUser = $data->fetch();

$telegramChatId = $dbUser['telegram_chat_id'] ?? null;
$email = $dbUser['email'] ?? $user['email'] ?? '';
$username = $dbUser['login'] ?? $user['login'] ?? '';
?>

<main class="container my-5 py-4 flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <h2 class="fw-bold mb-4 text-center">Налаштування профілю</h2>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Зміна логіна та Email -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Особисті дані</h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>/actions/update-profile.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Логін / Ім'я користувача</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?= htmlspecialchars($username) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email адреса</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($email) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Зберегти зміни</button>
                    </form>
                </div>
            </div>

            <!-- Connect / Disconnect Telegram -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Нагадування на Telegram</h5>
                        <small class="text-muted fw-normal d-block" style="font-size: 0.95rem;">
                            <b>(Ця функція поки не працює, знаходиться на стадії розробки)</b>
                        </small>
                </div>
                <div class="card-body">
                    <?php if (!empty($telegramChatId)): ?>
                        <div class="alert alert-success mb-3">
                            <strong>✅ Telegram успішно підключено!</strong><br>
                            <small class="text-muted">Ваш Chat ID: <?= htmlspecialchars($telegramChatId) ?></small>
                        </div>
                        <form action="<?= BASE_URL ?>/actions/disconnect-telegram.php" method="POST">
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-1"></i> Відключити Telegram
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="card-text text-muted">
                            Підключіть Telegram-бота, щоб отримувати миттєві нагадування про ваші задачі.
                        </p>
                        <a href="https://t.me/diloflow_remind_bot?start=<?= $user['id'] ?>"
                           target="_blank" class="btn btn-success w-100">
                            <i class="bi bi-telegram me-1"></i> Підключити Telegram
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Зміна пароля -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Зміна пароля</h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>/actions/update-password.php" method="POST">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Поточний пароль</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Новий пароль</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Підтвердження нового пароля</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-warning w-100">Оновити пароль</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>