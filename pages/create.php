<?php
session_start();
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';

if(!isset($_SESSION['user'])){
    $_SESSION['login_error'] = 'Будь ласка, увійдіть у ваш обліковий запис!';
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];

unset($_SESSION['errors'], $_SESSION['old']);

// Отримуємо list_id із запиту (якщо перейшли з конкретного списку)
$urlListId = isset($_GET['list_id']) && $_GET['list_id'] !== '' ? (int)$_GET['list_id'] : null;
$selectedListId = $old['list_id'] ?? $urlListId;

// Отримуємо списки користувача для випадаючого меню
$listsStmt = $pdo->prepare('SELECT id, title FROM `task_lists` WHERE user_id = :user_id ORDER BY created_at DESC');
$listsStmt->execute(['user_id' => $userId]);
$userLists = $listsStmt->fetchAll(PDO::FETCH_ASSOC);

$title = 'Створити задачу — DiloFlow';
require_once __DIR__ . '/../includes/header.php';
?>

<main class="container my-5 flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="mb-4 text-center">Додати нову задачу!</h1>
            <form action="../actions/add-task.php" method="POST" class="shadow p-4 rounded bg-light border">
                <div class="mb-3">
                    <label for="title" class="form-label">Назва задачі</label>
                    <input type="text" name="title" id="title" class="form-control <?= !empty($errors['title']) ? 'is-invalid' : '' ?>"
                        placeholder="Введіть текст задачі..." value="<?= htmlspecialchars($old['title'] ?? '') ?>">
                    <?php if (!empty($errors['title'])): ?>
                        <div class="invalid-feedback">
                            <?= implode('<br>', $errors['title']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Вибір списку / проєкту -->
                <div class="mb-3">
                    <label for="list_id" class="form-label">Список (проєкт)</label>
                    <select name="list_id" id="list_id" class="form-select">
                        <option value="">-- Без списку (Загальний) --</option>
                        <?php foreach ($userLists as $list): ?>
                            <option value="<?= $list['id'] ?>" <?= $selectedListId == $list['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($list['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="due_date" class="form-label">Кінцевий термін (Дедлайн)</label>
                    <input type="date" name="due_date" id="due_date" class="form-control <?= !empty($errors['due_date']) ? 'is-invalid' : '' ?>"
                        value="<?= htmlspecialchars($old['due_date'] ?? '') ?>">
                    <?php if (!empty($errors['due_date'])): ?>
                        <div class="invalid-feedback">
                            <?= implode('<br>', $errors['due_date']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Додати</button>
                    <a href="<?= BASE_URL ?>/pages/dashboard.php<?= $urlListId ? '?list_id=' . $urlListId : '' ?>" class="btn btn-secondary">Назад</a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>