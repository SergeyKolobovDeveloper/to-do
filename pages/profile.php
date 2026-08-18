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
                                   value="<?= htmlspecialchars($user['username'] ?? $user['login'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email адреса</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Зберегти зміни</button>
                    </form>
                </div>
            </div>

            <!-- Картка 2: Зміна пароля -->
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