<?php
session_start();

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];

unset($_SESSION['errors'], $_SESSION['old']);

require_once "../config/db.php";
require_once "../includes/security.php";

if(!isset($_SESSION['user'])){
    $_SESSION['login_error'] = 'Будь ласка, увійдіть у ваш обліковий запис!';
    header('Location:' . BASE_URL . '/auth/login.php');
    exit;
}

if(empty($_GET['id'])){
    header('Location: ' . BASE_URL . '/pages/dashboard.php');
    exit;
}

$id = (int)$_GET['id'];
$userId = $_SESSION['user']['id'];

// Отримуємо задачу
$sql = 'SELECT * FROM `tasks` WHERE id = :id AND user_id = :user_id';
$result = $pdo->prepare($sql);
$result->execute([
    'id' => $id,
    'user_id' => $userId
]);

$data = $result->fetch(PDO::FETCH_ASSOC);
if(!$data){
    header('Location: ' . BASE_URL . '/pages/dashboard.php');
    exit;
}

// Отримую всі списки користувача для випадаючого списку
$listsData = $pdo->prepare('SELECT id, title FROM `task_lists` WHERE user_id = :user_id ORDER BY created_at DESC');
$listsData->execute(['user_id' => $userId]);
$userLists = $listsData->fetchAll(PDO::FETCH_ASSOC);

// Універсальна конвертація
$remindAtValue = '';
$rawRemindAt = $old['remind_at'] ?? $data['remind_at'] ?? null;

if(!empty($rawRemindAt)) {
    if(!empty($old['remind_at'])) {
        $remindAtValue = $old['remind_at'];
    } else {
        $userTimezone = $_SESSION['user']['timezone'] ?? 'Europe/Kyiv';

        $dt = new DateTime($rawRemindAt, new DateTimeZone('UTC'));

        $dt->setTimezone(new DateTimeZone($userTimezone));

        $remindAtValue = $dt->format('Y-m-d\TH:i');
    }
}

$selectedNotifyVia = $old['notify_via'] ?? $data['notify_via'] ?? 'none';
$title = 'Редагувати задачу';
require_once __DIR__ . '/../includes/header.php';
?>

<main class="container my-5 flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="mb-4 text-center">Редагувати задачу</h1>
            <form action="../actions/edit-task.php" method="post" class="shadow p-4 rounded bg-light border">
                <div class="mb-3">
                    <label for="title" class="form-label font-weight-bold">Назва</label>
                    <input type="text" name="title" id="title" class="form-control <?= !empty($errors['title']) ? 'is-invalid' : '' ?>" 
                        value="<?= htmlspecialchars($old['title'] ?? decryptText($data['title'])) ?>">
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
                        <?php 
                            $selectedListId = $old['list_id'] ?? $data['list_id'];
                        ?>
                        <?php foreach ($userLists as $list): ?>
                            <option value="<?= $list['id'] ?>" <?= $selectedListId == $list['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($list['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="due_date" class="form-label">Дата виконання</label>
                    <input type="date" name="due_date" id="due_date" class="form-control" 
                        value="<?= htmlspecialchars($old['due_date'] ?? $data['due_date'] ?? '') ?>">
                </div>

                <h5 class="mb-3 text-primary"><i class="bi bi-bell"></i>Нагадування
                    <small class="text-muted fw-normal d-block" style="font-size: 0.95rem;">
                            <b>(Ця функція поки не працює, знаходиться на стадії розробки)</b>
                    </small>
                </h5>
                <div>
                    <label for="remind_at" class="form-label">Дата та час нагадування</label>
                    <input type="datetime-local" name="remind_at" id="remind_at" class="form-control" 
                            value="<?= htmlspecialchars($remindAtValue) ?>">
                </div>

                <div class="mb-3">
                    <label for="notify_via" class="form-label">Спосіб сповіщення</label>
                    <select name="notify_via" id="notify_via" class="form-select">
                        <option value="none" <?= $selectedNotifyVia === 'none' ? 'selected' : '' ?>>Не нагадувати!</option>
                        <option value="email" <?= $selectedNotifyVia === 'email' ? 'selected' : '' ?>>Тільки Email</option>
                        <option value="telegram" <?= $selectedNotifyVia === 'telegram' ? 'selected' : '' ?>>Тільки Telegram</option>
                        <option value="both" <?= $selectedNotifyVia === 'both' ? 'selected' : ''?>>Email + Telegram</option>
                    </select>
                </div>

                <input type="hidden" name="id" value="<?= $data['id'] ?>">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Зберегти зміни</button>
                    <a href="dashboard.php" class="btn btn-secondary">Назад на головну</a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>