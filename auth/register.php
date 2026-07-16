<?php
session_start();

$errors = $_SESSION['register_errors'] ?? [];
$oldValues = $_SESSION['old_values'] ?? [];
unset($_SESSION['register_errors'], $_SESSION['old_values']);

require_once __DIR__ . '/../includes/header.php';
?>
<main class="container my-5 flex-grow-1 d-flex align-items-center justify-content-center">
    <div class="card shadow" style="width: 100%; max-width: 400px;">
        <div class="card-body p-4">
            <h2 class="mb-4 text-center">Форма рейстрації</h2>
            <form action="../actions/register-handler.php" method="post">
                <div class="mb-3">
                    <label for="name" class="form-label"></label>
                    <input type="text" id="name" name="name"
                        class="form-control <?= !empty($errors['name']) ? 'is-invalid' : ''; ?>"
                        placeholder="Введіть ваше ім'я" 
                        value="<?= htmlspecialchars($oldValues['name'] ?? ''); ?>">
                    <?php if(!empty($errors['name'])): ?>
                        <div class='invalid-feedback'>
                            <?= implode(', ', $errors['name']); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="login" class="form-label"></label>
                    <input type="text" id="login" name="login" 
                        class="form-control <?= !empty($errors['login']) ? 'is-invalid' : ''; ?>" 
                        placeholder="Введіть унікальний логін"
                        value="<?= htmlspecialchars($oldValues['login'] ?? '') ?>">
                    <?php if(!empty($errors['login'])): ?>
                        <div class="invalid-feedback">
                            <?= implode(', ', $errors['login']); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label"></label>
                    <input type="email" id="email" name="email" 
                        class="form-control <?= !empty($errors['email']) ? 'is-invalid' : ''; ?>" 
                        placeholder="Введіть вашe електронну пошту"
                        value="<?= htmlspecialchars($oldValues['email'] ?? '') ?>">
                    <?php if(!empty($errors['email'])): ?>
                        <div class="invalid-feedback">
                            <?= implode(', ', $errors['email']); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label"></label>
                    <input type="password" id="password" name="password" 
                        class="form-control <?= !empty($errors['password']) ? 'is-invalid' : ''; ?>" 
                        placeholder="Пароль має бути мінімум 6 символів">
                    <?php if(!empty($errors['password'])): ?>
                        <div class="invalid-feedback">
                            <?= implode(', ', $errors['password']) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="password_confirm" class="form-label"></label>
                    <input type="password" id="password_confirm" name="password_confirm" 
                        class="form-control <?= !empty($errors['password_confirm']) ? 'is-invalid' : '';?>" 
                        placeholder="Повторіть ваш пароль">
                    <?php if(!empty($errors['password_confirm'])): ?>
                        <div class="invalid-feedback">
                            <?= implode(', ', $errors['password_confirm']) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <input type="submit" class="btn btn-success w-100 mb-3" value="Створити акаунт">
            </form>
        </div>
    </div>
</main>
<?php
require_once __DIR__ . '/../includes/footer.php';
?>