<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$userId = $_SESSION['user']['id'];

try {
    // Очищаємо telegram_chat_id для поточного користувача
    $sql = "UPDATE `users` SET `telegram_chat_id` = NULL WHERE `id` = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $userId]);

    $_SESSION['success'] = "Telegram успішно відключено від вашого акаунта!";
} catch (Exception $e) {
    $_SESSION['error'] = "Помилка при відключенні Telegram: " . $e->getMessage();
}

header('Location: ' . BASE_URL . '/pages/profile.php');
exit;