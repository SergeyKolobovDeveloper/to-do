<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/auth/password-recovery.php');
    exit;
}

$token = $_POST['token'] ?? '';
if (empty($token)) {
    $_SESSION['reset_error'] = 'Інформація відсутня або недійсна!';
    header('Location: ' . BASE_URL . '/auth/password-recovery.php');
    exit;
}
$errorBag = [
    'password' => [],
    'password_confirm' => []
];

$password = $_POST['password'] ?? '';
if (empty($password)) {
    $errorBag['password'][] = 'Поле не може бути пустим!';
} elseif (mb_strlen($password) < 6) {
    $errorBag['password'][] = 'Пароль не може бути меншим шести символів!';
}

$password_confirm = $_POST['password_confirm'] ?? '';
if (empty($password_confirm)) {
    $errorBag['password_confirm'][] = 'Поле не може бути пустим!';
} elseif ($password !== $password_confirm) {
    $errorBag['password_confirm'][] = 'Паролі повинні збігатися!';
}

$hasErrors = false;
foreach ($errorBag as $key => $error) {
    if (!empty($error)) {
        $hasErrors = true;
        break;
    }
}

if ($hasErrors) {
    $firstError = !empty($errorBag['password']) ? $errorBag['password'][0] : $errorBag['password_confirm'][0];

    $_SESSION['reset_error'] = $firstError;
    header('Location: ' . BASE_URL . '/auth/reset-password.php?token=' . $token);
    exit;
}

$sql = 'SELECT * FROM `users` WHERE `reset_token` = :token';
$result = $pdo->prepare($sql);
$result->execute([':token' => $token]);
$data = $result->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    $_SESSION['reset_error'] = 'Посилання недійсне або термін його дії закінчився!';
    header('Location: ' . BASE_URL . '/auth/password-recovery.php');
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = 'UPDATE `users` SET `password` = :password, `reset_token` = NULL WHERE `id` = :id';
$update_result = $pdo->prepare($sql);
$update_result->execute([':password' => $hashedPassword, ':id' => $data['id']]);

$_SESSION['success_message'] = 'Пароль успішно змінено! Тепер ви можете увійти з новим паролем.';
header('Location: ' . BASE_URL . '/auth/login.php');
exit;