<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/pages/profile.php');
    exit;
}

$user_id = $_SESSION['user']['id'];
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    $_SESSION['error'] = "Будь ласка, заповніть усі поля!";
    header('Location: ' . BASE_URL . '/pages/profile.php');
    exit;
}

if ($new_password !== $confirm_password) {
    $_SESSION['error'] = "Новий пароль та підтвердження не збігаються!";
    header('Location: ' . BASE_URL . '/pages/profile.php');
    exit;
}

if (mb_strlen($new_password) < 6) {
    $_SESSION['error'] = "Мінімальна довжина нового пароля — 6 символів!";
    header('Location: ' . BASE_URL . '/pages/profile.php');
    exit;
}

try {
    // Отримуємо поточний хеш пароля з БД
    $data = $pdo->prepare("SELECT password FROM users WHERE id = :id");
    $data->execute(['id' => $user_id]);
    $user = $data->fetch();

    if (!$user || !password_verify($current_password, $user['password'])) {
        $_SESSION['error'] = "Неправильний поточний пароль!";
        header('Location: ' . BASE_URL . '/pages/profile.php');
        exit;
    }

    // Хешуємо новий пароль та оновлюємо запис у БД
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $updateData = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
    $updateData->execute([
        'password' => $new_hash,
        'id' => $user_id
    ]);

    $_SESSION['success'] = "Пароль успішно змінено!";
} catch (PDOException $e) {
    $_SESSION['error'] = "Помилка при оновленні пароля.";
}

header('Location: ' . BASE_URL . '/pages/profile.php');
exit;