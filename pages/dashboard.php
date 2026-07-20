<?php
session_start();
if(!isset($_SESSION['user'])){
    header('Location: /to-do/auth/login.php');
    exit;
}
require_once __DIR__ . '/../config/db.php';

$userId = $_SESSION['user']['id'];

$sql = 'SELECT * FROM `tasks` WHERE `user_id` = :user_id';
$result = $pdo->prepare($sql);
$result->execute(['user_id' => $userId]);

$data = $result->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>
<main class="container my-5 flex-grow-1 d-flex align-items-center justify-content-center">
    <div class="container mt-5">
        <div class="text-center">
            <h1>Менеджер задач!</h1>
        </div>
        <?php if (empty($data)): ?>
        <div class="text-center py-5 border rounded bg-light shadow-sm">
            <i class="bi bi-clipboard-check display-1 text-muted"></i>
            <h3 class="mt-3 text-secondary">У вас поки немає створених задач</h3>
            <p class="text-muted">Час спланувати свої справи! Натисніть кнопку нижче, щоб створити першу задачу.</p>
            <a href="create.php" class="btn btn-success btn-lg mt-2">
                <i></i>Додати першу задачу
            </a>
        </div>
        <?php else: ?>
        <div>
            <a href="create.php" class="btn btn-success mb-3">Додати задачу!</a>
        </div>
        <br>
        <div class="text-center">
            <table class="table table-bordered border-primary">
                <tr>
                    <th>Назва</th>
                    <th>Дії</th>
                    <th style="width: 160px;" class="text-center">Статус</th>
                </tr>
                <?php foreach($data as $item):?>
                    <tr>
                        <td><a href="../actions/status-task.php?id=<?= $item['id']?>"><?=htmlspecialchars($item['title'])?></a></td>
                        <td>
                            <div>
                                <a href="update.php?id=<?=$item['id'] ?>"  class="btn btn-primary btn-sm">Редагувати</a>
                                <a href="../actions/delete-task.php?id=<?= $item['id']?>"  class="btn btn-danger btn-sm">Видалити</a>
                            </div>
                        </td>
                        <td>
                            <?php if($item['is_completed'] === 1):?>
                                <span class="badge bg-success">Виконано!</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Не виконано!</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    <?php endif; ?>
</main>
<?php
require_once __DIR__ . '/../includes/footer.php';
?>