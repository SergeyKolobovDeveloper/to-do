<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/mail.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: ' . BASE_URL . '/auth/password-recovery.php');
    exit;
}

$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    $_SESSION['pass_recovery_error'] = 'Будь ласка, введіть ваш Email!';
    header('Location: ' . BASE_URL . '/auth/password-recovery.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['pass_recovery_error'] = 'Введіть коректну електронну адресу!';
    header('Location: ' . BASE_URL . '/auth/password-recovery.php');
    exit;
}

$sql = 'SELECT * FROM `users` WHERE `email` = :email';
$data = $pdo->prepare($sql);
$data->execute([':email' => $email]);
$user = $data->fetch(PDO::FETCH_ASSOC);

if(!$user) {
    $_SESSION['pass_recovery_error'] = 'Користувача з таким Email не знайдено!';
    header('Location: ' . BASE_URL . '/auth/password-recovery.php');
    exit;
}

$token = bin2hex(random_bytes(32));

$sql = 'UPDATE `users` SET `reset_token` = :token WHERE `id` = :id';
$updateData = $pdo->prepare($sql);
$updateData->execute([':token' => $token, ':id' => $user['id']]);

$resetLink = BASE_URL . '/auth/reset-password.php?token=' . $token;

$subject = 'Відновлення пароля в TaskFlow';
$body = "<h2>Вітаємо, " . htmlspecialchars($user['name']) . "!</h2>
    <p>Ми отримали запит на скидання пароля для вашого акаунту в TaskFlow.</p>
    <p>Щоб встановити новий пароль, натисніть на кнопку нижче:</p>
    <p><a href='" . $resetLink . "' style='padding: 10px 15px; background-color: #0d6efd; color: #ffffff; text-decoration: none; border-radius: 5px; display: inline-block;'>Встановити новий пароль</a></p>
    <p>Або скопіюйте це посилання в браузер:<br>" . $resetLink . "</p>
    <p><small>Якщо ви не запитували скидання пароля, просто ігноруйте цей лист.</small></p>";
sendMail($email, $subject, $body);

$_SESSION['pass_recovery_success'] = 'Інструкцію зі скидання пароля надіслано на вашу пошту!';
header('Location: ' . BASE_URL . '/auth/password-recovery.php');
exit;