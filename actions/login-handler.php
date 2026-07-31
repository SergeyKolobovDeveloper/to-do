<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$errorBag = [
    'email' => [],
    'password' => []
];

$email = trim($_POST['email'] ?? '');
if (empty($email)) {
    $errorBag['email'][] = 'Поле не може бути пустим!';
} 

$password = trim($_POST['password'] ?? '');
if (empty($password)) {
    $errorBag['password'][] = 'Поле не може бути пустим!';
}

$hasErrors = false;

foreach ($errorBag as $key => $error) {
    if (!empty($error)) {
        $hasErrors = true;
        break;
    }
}

if ($hasErrors) {
    $_SESSION['login_errors'] = $errorBag;
    $_SESSION['old_values'] = ['email' => $email];

    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$sql = 'SELECT * FROM `users` WHERE `email` = :email';

$result = $pdo->prepare($sql);
$result->execute(['email' => $email]);
$data = $result->fetch(PDO::FETCH_ASSOC);

if (!$data || !password_verify($password, $data['password'])) {
    $errorBag['email'][] = 'Невірний email або пароль!';

    $_SESSION['login_errors'] = $errorBag;
    $_SESSION['old_values'] = ['email' => $email];

    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

if ((int)$data['is_verified'] === 0) {
    $errorBag['email'][] = 'Ваш email ще не підтверджено. Перевірте пошту!';

    $_SESSION['login_errors'] = $errorBag;
    $_SESSION['old_values'] = ['email' => $email];

    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$_SESSION['user'] = [
    'id'          => $data['id'],
    'name'        => $data['name'],
    'email'       => $data['email'],
    'is_verified' => $data['is_verified']
];

unset($_SESSION['old_values']);

header('Location: ' . BASE_URL . '/pages/dashboard.php');
exit;