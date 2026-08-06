<?php
session_start();

require_once '../config/db.php';
require_once '../includes/security.php';

if (!isset($_SESSION['user'])) {
    $_SESSION['login_error'] = 'Будь ласка, увійдіть у систему!';
    header('Location:' . BASE_URL . '/auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: ../pages/dashboard.php');
    exit;
}

$id = (int)$_POST['id'];
$title = trim($_POST['title'] ?? '');
$dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
$userId = $_SESSION['user']['id'];

$errorBag = [
    'title' => [],
];

if (empty($title)) {
    $errorBag['title'][] = 'Поле не може бути пустим!';
} elseif (mb_strlen($title) < 3) {
    $errorBag['title'][] = 'Назва задачі має містити щонайменше 3 символи!';
} elseif (mb_strlen($title) > 255) {
    $errorBag['title'][] = 'Назва задачі надто довга (максимум 255 символів)!';
}

if (!empty($errorBag['title'])) {
    $_SESSION['errors'] = $errorBag;
    $_SESSION['old'] = [
        'title' => decryptText($title),
        'due_date' => $dueDate
    ];
    header('Location: ../pages/update.php?id=' . $id);
    exit;
}

$encryptedTitle = encryptText($title);

$sql = 'UPDATE `tasks` SET title = :title, due_date = :due_date WHERE id = :id AND user_id = :user_id';

$result = $pdo->prepare($sql);

$result->execute([
    'title' => $encryptedTitle,
    'due_date' => $dueDate,
    'id' => $id,
    'user_id' => $userId
]);

$_SESSION['success'] = 'Зміни успішно збережено! 💾';

header('Location: ../pages/dashboard.php');
exit;