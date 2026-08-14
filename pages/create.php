<?php
session_start();
require_once __DIR__ . '/../config/app.php';

if(!isset($_SESSION['user'])){
    $_SESSION['login_error'] = 'Будь ласка, увійдіть у ваш обліковий запис!';
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];

unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Create</title>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1 class="mb-4 text-center">Додати нову задачу!</h1>
                <form action="../actions/add-task.php" method="POST" class="shadow p-4 rounded bg-light">
                    <div class="mb-3">
                        <label for="title" class="from-label">Назва задачі</label>
                        <input type="text" name="title" id="title" class="form-control <?= !empty($errors['title']) ? 'is-invalid' : '' ?>"
                            placeholder="Введіть текст задачі..." value="<?= htmlspecialchars($old['title'] ?? '') ?>">
                        <?php if (!empty($errors['title'])): ?>
                            <div class="invalid-feedback">
                                <?= implode('<br>', $errors['title']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="from-label">Кінцевий термін (Дедлайн)</label>
                        <input type="date" name="due_date" id="due_date" class="from-control <?= !empty($errors['due_date']) ? 'is-invalid' : '' ?>"
                            value="<?= htmlspecialchars($old['due_date'] ?? '') ?>">
                        <?php if (!empty($errors['due_date'])): ?>
                            <div class="invalid-feedback">
                                <?= implode('<br>', $errors['due_date']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Додати</button>
                        <a href="<?= BASE_URL ?>/pages/dashboard.php" class="btn btn-secondary">Назад на головну</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>