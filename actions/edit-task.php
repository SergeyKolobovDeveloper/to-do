<?php
session_start();

require_once '../config/db.php';
require_once '../includes/security.php';

if (!isset($_SESSION['user'])) {
    $_SESSION['login_error'] = 'Будь ласка, увійдіть у систему!';
    header('Location:' . BASE_URL . '/auth/login.php');
    exit;
}

//Перевірка методу POST та наявності ID
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: ../pages/dashboard.php');
    exit;
}

// Зчитування даних з форми
$id = (int)$_POST['id'];
$title = trim($_POST['title'] ?? '');
$dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
$listId = !empty($_POST['list_id']) ? (int)$_POST['list_id'] : null;

$remindAt = null;
if (!empty($_POST['remind_at'])) {
    $dt = new DateTime($_POST['remind_at'], new DateTimeZone('Europe/Kyiv'));
    $dt->setTimezone(new DateTimeZone('UTC'));
    $remindAt = $dt->format('Y-m-d H:i:s');
}

$notifyVia = $_POST['notify_via'] ?? 'none';
$userId = $_SESSION['user']['id'];


$errorBag = [
    'title' => [],
    'remind_at' => []
];


if (empty($title)) {
    $errorBag['title'][] = 'Поле не може бути пустим!';
} elseif (mb_strlen($title) < 3) {
    $errorBag['title'][] = 'Назва задачі має містити щонайменше 3 символи!';
} elseif (mb_strlen($title) > 500) {
    $errorBag['title'][] = 'Назва задачі надто довга (максимум 500 символів)!';
}

// Перевірка: якщо виникли помилки — повертаємо назад
if (!empty($errorBag['title']) || !empty($errorBag['remind_at'])) {
    $_SESSION['errors'] = $errorBag;
    $_SESSION['old'] = [
        'title' => $title,
        'due_date' => $dueDate,
        'list_id' => $listId,
        'remind_at' => $remindAt,
        'notify_via' => $notifyVia
    ];
    header('Location: ../pages/update.php?id=' . $id);
    exit;
}

$encryptedTitle = encryptText($title);

// Оновлення в БД (додано is_reminded = 0)
$sql = 'UPDATE `tasks` 
        SET title = :title, 
            due_date = :due_date, 
            list_id = :list_id, 
            remind_at = :remind_at, 
            notify_via = :notify_via,
            is_reminded = 0
        WHERE id = :id AND user_id = :user_id';

$result = $pdo->prepare($sql);

$result->execute([
    'title' => $encryptedTitle,
    'due_date' => $dueDate,
    'list_id' => $listId,
    'remind_at' => $remindAt,
    'notify_via' => $notifyVia,
    'id' => $id,
    'user_id' => $userId
]);

$_SESSION['success'] = 'Зміни успішно збережено!';

// Повернення на dashboard із збереженням обраного списку
header('Location: ../pages/dashboard.php' . ($listId ? '?list_id=' . $listId : ''));
exit;