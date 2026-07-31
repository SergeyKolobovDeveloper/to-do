<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/mail.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/auth/register.php');
    exit;
}

$errorBag = [
    'name' => [],
    'login' => [],
    'email' => [],
    'password' => [],
    'password_confirm' => []
];

$name = trim($_POST['name'] ?? '');
if (empty($name)) {
    $errorBag['name'][] = 'Поле не може бути пустим!';
}

$login = trim($_POST['login'] ?? '');
if (empty($login)) {
    $errorBag['login'][] = 'Поле не може бути пустим!';
} elseif (mb_strlen($login) < 3) {
    $errorBag['login'][] = 'Логін має бути не менше трьох символів!';
}

$email = trim($_POST['email'] ?? '');
if (empty($email)) {
    $errorBag['email'][] = 'Поле не може бути пустим!';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errorBag['email'][] = 'Введіть правильний email!';
}

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
    $_SESSION['register_errors'] = $errorBag;
    $_SESSION['old_values'] = [
        'name' => $name,
        'login' => $login,
        'email' => $email
    ];
    header('Location: ' . BASE_URL . '/auth/register.php');
    exit;
}

$sql = 'SELECT * FROM `users` WHERE `login` = :login OR `email` = :email';
$result = $pdo->prepare($sql);
$result->execute([
    'login' => $login,
    'email' => $email
]);
$data = $result->fetch(PDO::FETCH_ASSOC);

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(32));

if ($data) {
    if ((int)$data['is_verified'] === 1) {
        if ($data['login'] === $login) {
            $errorBag['login'][] = 'Такий логін вже зайнятий, спробуйте інший!';
        }
        if ($data['email'] === $email) {
            $errorBag['email'][] = 'Цей email вже зареєстрований!';
        }

        $_SESSION['register_errors'] = $errorBag;
        $_SESSION['old_values'] = [
            'name' => $name,
            'login' => $login,
            'email' => $email
        ];
        header('Location: ' . BASE_URL . '/auth/register.php');
        exit;
    }

    if ($data['login'] === $login && $data['email'] !== $email) {
        $errorBag['login'][] = 'Такий логін вже зайнятий, спробуйте інший!';
        $_SESSION['register_errors'] = $errorBag;
        $_SESSION['old_values'] = [
            'name' => $name,
            'login' => $login,
            'email' => $email
        ];
        header('Location: ' . BASE_URL . '/auth/register.php');
        exit;
    }

    $updateSql = 'UPDATE `users` 
                  SET `name` = :name, `login` = :login, `password` = :password, `verification_token` = :token 
                  WHERE `email` = :email AND `is_verified` = 0';
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([
        ':name' => $name,
        ':login' => $login,
        ':password' => $hashedPassword,
        ':token' => $token,
        ':email' => $email
    ]);

} else {
    $sql = 'INSERT INTO `users` (`name`, `login`, `email`, `password`, `verification_token`, `is_verified`) 
            VALUES (:name, :login, :email, :password, :token, 0)';

    $result = $pdo->prepare($sql);
    $result->execute([
        ':name' => $name,
        ':login' => $login,
        ':email' => $email,
        ':password' => $hashedPassword,
        ':token' => $token
    ]);
}

$verifyLink = BASE_URL . '/auth/verify.php?token=' . $token;

$subject = 'Підтвердження реєстрації в TaskFlow';
$body = "
    <h2>Вітаємо, " . htmlspecialchars($name) . "!</h2>
    <p>Дякуємо за реєстрацію в TaskFlow.</p>
    <p>Щоб активувати свій акаунт, переходьте за посиланням нижче:</p>
    <p><a href='" . $verifyLink . "' style='padding: 10px 15px; background-color: #28a745; color: #ffffff; text-decoration: none; border-radius: 5px; display: inline-block;'>Підтвердити пошту</a></p>
    <p>Або скопіюйте це посилання в браузер:<br>" . $verifyLink . "</p>";

sendMail($email, $subject, $body);

unset($_SESSION['old_values']);

$_SESSION['success_message'] = 'Реєстрація успішна! Ми відправили новий лист для підтвердження на вашу пошту. Якщо не бачите листа в «Вхідних», обов’язково перевірте папку «Спам»!';
header('Location: ' . BASE_URL . '/auth/login.php');
exit;