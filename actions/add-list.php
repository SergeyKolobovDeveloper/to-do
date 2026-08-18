<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

// Перевіряємо авторизацію та метод POST
if (!isset($_SESSION['user']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/pages/lists.php');
    exit;
}

$user_id = $_SESSION['user']['id'];
$title = trim($_POST['title'] ?? '');

// Валідація
if (empty($title)) {
    $_SESSION['error'] = "Будь ласка, введіть назву списку!";
    header('Location: ' . BASE_URL . '/pages/lists.php');
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO `task_lists` (`user_id`, `title`) VALUES (:user_id, :title)");
    $stmt->execute([
        'user_id' => $user_id,
        'title' => $title
    ]);

    $_SESSION['success'] = "Новий список успішно створено!";
} catch (PDOException $e) {
    $_SESSION['error'] = "Помилка при створенні списку.";
}

header('Location: ' . BASE_URL . '/pages/lists.php');
exit;