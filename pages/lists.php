<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

// Захист сторінки
if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$userId = $_SESSION['user']['id'];

// Отримуємо загальну кількість усіх задач користувача
$totalStmt = $pdo->prepare('SELECT COUNT(*) FROM `tasks` WHERE user_id = :user_id');
$totalStmt->execute(['user_id' => $userId]);
$totalAllTasks = $totalStmt->fetchColumn();

// Отримуємо списки користувача та кількість задач у кожному списку
$stmt = $pdo->prepare('
    SELECT tl.*, COUNT(t.id) as total_tasks 
    FROM `task_lists` tl 
    LEFT JOIN `tasks` t ON tl.id = t.list_id 
    WHERE tl.user_id = :user_id 
    GROUP BY tl.id 
    ORDER BY tl.id DESC
');
$stmt->execute(['user_id' => $userId]);
$lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

$title = "Мої списки задач — DiloFlow";
require_once __DIR__ . '/../includes/header.php';
?>

<main class="container my-4 flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Мої списки та проєкти</h2>
        <!-- Кнопка відкриття модального вікна для додавання списку -->
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addListModal">
            + Створити список
        </button>
    </div>

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

    <div class="row row-cols-1 row-cols-md-3 g-4">
        
        <!-- Картка: Усі задачі (завжди перша) -->
        <div class="col">
            <div class="card h-100 shadow-sm border-primary bg-primary bg-opacity-10">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title fw-bold text-primary">
                            <i class="bi bi-layers-fill me-2"></i>Усі задачі
                        </h5>
                        <p class="card-text text-muted">
                            Всього завдань: <strong><?= $totalAllTasks ?></strong>
                        </p>
                    </div>
                    <div class="mt-3">
                        <a href="<?= BASE_URL ?>/pages/dashboard.php" class="btn btn-primary btn-sm w-100 fw-semibold">
                            Переглянути всі
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Користувацькі списки -->
        <?php foreach ($lists as $list): ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-0 bg-light">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title fw-bold text-dark">
                                <i class="bi bi-folder2-open me-2 text-secondary"></i><?= htmlspecialchars($list['title']) ?>
                            </h5>
                            <p class="card-text text-muted">
                                Завдань у списку: <strong><?= $list['total_tasks'] ?></strong>
                            </p>
                        </div>
                        <div class="mt-3">
                            <a href="<?= BASE_URL ?>/pages/dashboard.php?list_id=<?= $list['id'] ?>" class="btn btn-outline-primary btn-sm w-100">
                                Переглянути завдання
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>

    <!-- Модальне вікно для створення списку -->
    <div class="modal fade" id="addListModal" tabindex="-1" aria-labelledby="addListModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?= BASE_URL ?>/actions/add-list.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addListModalLabel">Новий список</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Назва списку / проєкту</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Наприклад: Навчання" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                        <button type="submit" class="btn btn-success">Зберегти</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>