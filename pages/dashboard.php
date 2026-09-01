<?php
session_start();
$title = 'Personal Account';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/security.php';

if(!isset($_SESSION['user'])){
    $_SESSION['login_error'] = 'Будь ласка, увійдіть у ваш обліковий запис!';
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$filter = $_GET['filter'] ?? 'all';
$sort = $_GET['sort'] ?? 'date_asc';
$listId = isset($_GET['list_id']) && $_GET['list_id'] !== '' ? (int)$_GET['list_id'] : null;

// Назва списку (якщо обрано конкретний список)
$currentListTitle = null;
if ($listId) {
    $listData = $pdo->prepare('SELECT title FROM `task_lists` WHERE id = :id AND user_id = :user_id');
    $listData->execute(['id' => $listId, 'user_id' => $userId]);
    $currentListTitle = $listData->fetchColumn();
}

switch($sort) {
    case 'date_desc':
        $orderBy = 't.due_date IS NULL ASC, t.due_date DESC, t.id DESC';
        break;
    case 'newest':
        $orderBy = 't.id DESC';
        break;
    case 'date_asc':
    default:
        $orderBy = 't.due_date IS NULL ASC, t.due_date ASC, t.id DESC';
        break;
}

// Формуємо базовий SQL-запит із підтягуванням назви списку
$sql = 'SELECT t.*, tl.title AS list_title 
        FROM `tasks` t 
        LEFT JOIN `task_lists` tl ON t.list_id = tl.id 
        WHERE t.`user_id` = :user_id';
$params = ['user_id' => $userId];

// Додаємо фільтр за списком (якщо обрано)
if ($listId) {
    $sql .= ' AND t.`list_id` = :list_id';
    $params['list_id'] = $listId;
}

// Додаємо фільтр за статусом
if ($filter === 'active'){
    $sql .= ' AND t.is_completed = 0';
} elseif ($filter === 'completed') {
    $sql .= ' AND t.is_completed = 1';
}

$perPage = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$countSql = 'SELECT COUNT(*) FROM `tasks` t WHERE t.`user_id` = :user_id';
$countParams = ['user_id' => $userId];

if($listId) {
    $countSql .= ' AND t.`list_id` = :list_id';
    $countParams['list_id'] = $listId;
}

if ($filter === 'active') {
    $countSql .= ' AND t.is_completed = 0';
} elseif ($filter === 'completed') {
    $countSql .= ' AND t.is_completed = 1';
}

$countData = $pdo->prepare($countSql);
$countData->execute($countParams);
$filterTaskCount = $countData->fetchColumn();

$totalPages = ceil($filterTaskCount / $perPage);

if($totalPages > 0 && $page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $perPage;

$sql .= " ORDER BY {$orderBy} LIMIT :limit OFFSET :offset";

$result = $pdo->prepare($sql);

foreach ($params as $key => $el) {
    $result->bindValue(":{$key}", $el);
}

$result->bindValue(':limit', $perPage, PDO::PARAM_INT);
$result->bindValue(':offset', $offset, PDO::PARAM_INT);

$result->execute();
$data = $result->fetchAll(PDO::FETCH_ASSOC);

// Отримуємо загальну кількість задач користувача (для перевірки порожнього стану)
$totalSql = 'SELECT COUNT(*) FROM `tasks` WHERE `user_id` = :user_id';
$totalParams = ['user_id' => $userId];
if ($listId) {
    $totalSql .= ' AND `list_id` = :list_id';
    $totalParams['list_id'] = $listId;
}
$totalData = $pdo->prepare($totalSql);
$totalData->execute($totalParams);
$totalTaskCount = $totalData->fetchColumn();

$today = date('Y-m-d');

// Параметр list_id для посилань фільтрації та сортування
$listParam = $listId ? "&list_id={$listId}" : "";

require_once __DIR__ . '/../includes/header.php';
?>
<main class="container my-4 flex-grow-1">
    <div class="text-center mb-4">
        <h1>
            <?= $currentListTitle ? "Список: " . htmlspecialchars($currentListTitle) : "Менеджер задач!" ?>
        </h1>
        <?php if ($currentListTitle): ?>
            <a href="<?= BASE_URL ?>/pages/lists.php" class="btn btn-outline-secondary btn-sm mt-1">
                ← Повернутися до всіх списків
            </a>
        <?php endif; ?>
    </div>

<?php if ($totalTaskCount == 0): ?>
    <div class="text-center py-5 border rounded bg-light shadow-sm">
        <i class="bi bi-clipboard-check display-1 text-muted"></i>
        <h3 class="mt-3 text-secondary">У вас поки немає створених задач</h3>
        <p class="text-muted">Час спланувати свої справи! Натисніть кнопку нижче, щоб створити першу задачу.</p>
        <a href="create.php<?= $listId ? '?list_id=' . $listId : '' ?>" class="btn btn-success btn-lg mt-2">Додати першу задачу</a>
    </div>
<?php else: ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>/pages/create.php<?= $listId ? '?list_id=' . $listId : '' ?>" class="btn btn-success">
                Додати задачу!
            </a>
            <?php if ($listId): ?>
                <a href="<?= BASE_URL ?>/pages/dashboard.php" class="btn btn-outline-secondary">
                    📋 Усі задачі
                </a>
            <?php endif; ?>
        </div>
        <a href="<?= BASE_URL ?>/pages/lists.php" class="btn btn-outline-success">
            До списків та проєктів
        </a>
    </div>
    <br>
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']);?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="btn-group" role="group" aria-label="Фільтр задач">
            <a href="<?= BASE_URL ?>/pages/dashboard.php?filter=all&sort=<?= $sort ?><?= $listParam ?>" class="btn btn-outline-primary <?= $filter === 'all' ? 'active' : '' ?>">Всі</a>
            <a href="<?= BASE_URL ?>/pages/dashboard.php?filter=active&sort=<?= $sort ?><?= $listParam ?>" class="btn btn-outline-warning <?= $filter === 'active' ? 'active' : '' ?>">Активні</a>
            <a href="<?= BASE_URL ?>/pages/dashboard.php?filter=completed&sort=<?= $sort ?><?= $listParam ?>" class="btn btn-outline-success <?= $filter === 'completed' ? 'active' : '' ?>">Виконані</a>
        </div>

        <div class="dropdown">
            <button class="btn btn-outline-success btn-sm dropdown-toggle d-flex align-items-center gap-1" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-funnel"></i> 
                <span>
                    <?php 
                        if ($sort === 'date_asc') echo 'За найближчою датою';
                        elseif ($sort === 'date_desc') echo 'За пізнішою датою';
                        else echo 'За замовчуванням';
                    ?>
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="sortDropdown">
                <li>
                    <a class="dropdown-item <?= $sort === 'date_asc' ? 'active' : '' ?>" href="<?= BASE_URL ?>/pages/dashboard.php?filter=<?= $filter ?>&sort=date_asc<?= $listParam ?>">
                        За найближчою датою
                    </a>
                </li>
                <li>
                    <a class="dropdown-item <?= $sort === 'date_desc' ? 'active' : '' ?>" href="<?= BASE_URL ?>/pages/dashboard.php?filter=<?= $filter ?>&sort=date_desc<?= $listParam ?>">
                        За пізнішою датою
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item <?= $sort === 'newest' ? 'active' : '' ?>" href="<?= BASE_URL ?>/pages/dashboard.php?filter=<?= $filter ?>&sort=newest<?= $listParam ?>">
                        За замовчуванням
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered border-primary align-middle text-center">
            <thead>
                <tr class="align-middle text-center">
                    <th class="text-center">Назва
                        <small class="text-muted fw-normal d-block" style="font-size: 0.75rem;">
                            (натисніть на задачу, щоб змінити її статус)
                        </small>
                    </th>
                    <?php if (!$listId): ?>
                        <th style="width: 150px;">Список</th>
                    <?php endif; ?>
                    <th style="width: 180px;">Дедлайн</th>
                    <th style="width: 170px;">Нагадування
                        <small class="text-muted fw-normal d-block" style="font-size: 0.75rem;">
                            (Ця функція поки не працює, знаходиться на стадії розробки)
                        </small>
                    </th>
                    <th style="width: 200px;">Дії</th>
                    <th style="width: 160px;">Статус</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($data)): ?>
                <tr>
                    <td colspan="<?= $listId ? '4' : '5' ?>">
                        <strong>
                            <?= $filter === 'active' ? 'Поки немає активних задач!' : 
                            ($filter === 'completed' ? 'Поки немає виконаних задач! ' : '') ?>
                        </strong>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach($data as $item):?>
                <tr>
                    <td class="text-start">
                        <a href="../actions/status-task.php?id=<?= $item['id']?>&filter=<?= $filter ?><?= $listParam ?>">
                        <?= htmlspecialchars(decryptText($item['title'])) ?>
                        </a>
                    </td>
                    <?php if (!$listId): ?>
                        <td>
                            <?php if (!empty($item['list_title'])): ?>
                                <span class="badge bg-info text-dark">
                                    <?= htmlspecialchars($item['list_title']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted" style="font-size: 0.85rem;">Загальний</span>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                    <td>
                    <?php if (!empty($item['due_date'])): ?>
                        <?php
                            $formatDate = date('d.m.Y', strtotime($item['due_date']));
                        ?>
                        <?php if ($item['is_completed'] == 0 && $item['due_date'] < $today): ?>
                            <span class="badge bg-danger" title="Термін виконання минув!">
                                <?= $formatDate ?> (Протерміновано!)
                            </span>
                        <?php else: ?>
                            <span class="text-dark"><?= $formatDate ?></span>
                        <?php endif; ?>
                    <?php else: ?>
                            <span class="text-muted">Термін не вказано!</span>
                    <?php endif; ?>
                    </td>
                    <td class="text-center aling-middle">
                        <?php if ($item['notify_via'] === 'both'):?>
                            <span class="badge bg-primary md-1 d-inline-blog">
                                <i class="bi bi-telegram"></i> Telegram
                            </span>
                            <span class="badge bg-info text-dark d-inline-blpck">
                                <i class="bi bi-envelope-at"></i> Email
                            </span>
                        <?php elseif ($item['notify_via'] === 'telegram'):?>
                            <span class="badge bg-primary">
                                <i class="bi bi-telegram"></i> Telegram
                            </span>
                        <?php elseif ($item['notify_via'] === 'email'):?>
                            <span class="badge bg-info text-dark ">
                                <i class="bi bi-envelope-at"></i> Email
                            </span>
                        <?php else:?>
                            <span class="text-muted small">Немає</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-nowrap">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <a href="update.php?id=<?= $item['id'] ?><?= !empty($listId) ? '&list_id=' . $listId : '' ?>" class="btn btn-primary btn-sm">
                                Редагувати
                            </a>
                            <a href="../actions/delete-task.php?id=<?= $item['id'] ?><?= !empty($listId) ? '&list_id=' . $listId : '' ?>" 
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('Ви дійсно хочете видалити цю задачу?');">
                                Видалити
                            </a>
                        </div>
                    </td>
                    <td>
                    <?php if($item['is_completed'] == 1):?>
                        <span class="badge bg-success">Виконано!</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Не виконано!</span>
                    <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if($totalPages > 1): ?>
        <nav aria-label="Пагінація задач" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&filter=<?= $filter ?>&sort=<?= $sort ?><?= $listParam ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>
</main>
<?php
require_once __DIR__ . '/../includes/footer.php';
?>