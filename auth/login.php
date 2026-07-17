<?php
session_start();

$errors = $_SESSION['login_errors'] ?? [];
$oldValues = $_SESSION['old_values'] ?? [];
unset($_SESSION['login_errors'], $_SESSION['old_values']);

require_once __DIR__ . '/../includes/header.php';
?>
<main class="container my-5 flex-grow-1 d-flex align-items-center justify-content-center">
    <div class="card shadow" style="width: 100%; max-width: 400px;">
        <div class="card-body p-4">
            <h2 class="mb-4 text-center">Авторизація</h2>
            <form action="../actions/login-handler.php" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label"></label>
                    <input type="email" name="email" id="email" 
                        class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '';?>"
                        placeholder="Введіть ваш email"
                        value="<?= htmlspecialchars($oldValues['email'] ?? '');?>">
                    <?php if(!empty($errors['email'])):?>
                        <div class="invalid-feedback">
                            <?= implode(', ' , $errors['email']) ?>
                        </div>
                    <?php endif ?>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label"></label>
                    <input type="password" name="password" id="password" 
                        class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '';?>" 
                        placeholder="Введіть ваш пароль">
                    <?php if(!empty($errors['password'])): ?>
                        <div class="invalid-feedback">
                            <?= implode(', ', $errors['password']) ?>
                        </div>
                    <?php endif ?>
                </div>
                    <input type="submit" class="btn btn-success w-100 mb-3" value="Увійти">
                <div class="text-center mt-3 small">
                    <p class="mb-1 text-muted">
                        Не вдаєть увійти? <a href="#" class="text-decoration-none link-success fw-semibold">Відновити акаунт</a>
                    </p>
                    <p class="mb-0 text-muted">
                        Не має акаунту? <a href="/to-do/auth/register.php" class="text-decoration-none link-success fw-semibold">Створити акаунт</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</main>
<?php
require_once __DIR__ . '/../includes/footer.php';