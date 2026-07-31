<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$token = $_GET['token'] ?? '';

if (empty($token)) {
    $_SESSION['login_error'] = 'Недійсне або відсутнє посилання для підтвердження.';
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$sql = 'SELECT `id` FROM `users` WHERE `verification_token` = :token LIMIT 1';
$data = $pdo->prepare($sql);
$data->execute([':token' => $token]);
$user = $data->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $updateSql = 'UPDATE `users` SET `is_verified` = 1, `verification_token` = NULL WHERE `id` = :id';
    $updateData = $pdo->prepare($updateSql);
    $updateData->execute([':id' => $user['id']]);

    $_SESSION['success_message'] = 'Вашу пошту успішно підтверджено! Тепер ви можете увійти.';
} else {
    $_SESSION['login_error'] = 'Посилання підтвердження недійсне або застаріле.';
}

header('Location: ' . BASE_URL . '/auth/login.php');
exit;