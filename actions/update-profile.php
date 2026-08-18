<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

// Перевірка авторизації та методом POST
if (!isset($_SESSION['user']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/pages/profile.php');
    exit;
}

$user_id = $_SESSION['user']['id'];
$login = trim($_POST['username'] ?? $_POST['login'] ?? '');
$email = trim($_POST['email'] ?? '');

if (empty($login) || empty($email)) {
    $_SESSION['error'] = "Усі поля повинні бути заповнені!";
    header('Location: ' . BASE_URL . '/pages/profile.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Введіть коректну email-адресу!";
    header('Location: ' . BASE_URL . '/pages/profile.php');
    exit;
}

try {
    // Перевірка, чи не зайнятий новий login або email іншим користувачем
    $data = $pdo->prepare("SELECT id FROM users WHERE (login = :login OR email = :email) AND id != :id");
    $data->execute([
        'login' => $login,
        'email' => $email,
        'id' => $user_id
    ]);

    if ($data->fetch()) {
        $_SESSION['error'] = "Цей логін або email вже використовується іншим користувачем!";
        header('Location: ' . BASE_URL . '/pages/profile.php');
        exit;
    }

    $updateData = $pdo->prepare("UPDATE users SET login = :login, email = :email WHERE id = :id");
    $updateData->execute([
        'login' => $login,
        'email' => $email,
        'id' => $user_id
    ]);

    // Оновлюємо дані в поточній сесії
    $_SESSION['user']['login'] = $login;
    $_SESSION['user']['email'] = $email;

    $_SESSION['success'] = "Особисті дані успішно оновлено!";
} catch (PDOException $e) {
    $_SESSION['error'] = "Помилка при оновленні даних у базі.";
}

header('Location: ' . BASE_URL . '/pages/profile.php');
exit;