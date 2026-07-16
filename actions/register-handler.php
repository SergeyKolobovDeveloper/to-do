<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: /to-do/auth/register.php');
    exit;
}
   
    $errorBag = [
        'name' => [],
        'login' => [],
        'email' => [],
        'password' => [],
        'password_confirm' => []
    ];

    $name = trim($_POST['name']);
        if(empty($name)){
            $errorBag['name'][] = 'Поле не може бути пустим!';
        }

    $login = trim($_POST['login']);
        if(empty($login)){
            $errorBag['login'][] = 'Поле не може бути пустим!';
        } elseif(mb_strlen($login) < 3) {
            $errorBag['login'][] = 'Логін має бути не менше трьох символів!';
        }

    $email = trim($_POST['email']);
        if(empty($email)){
            $errorBag['email'][] = 'Поле не може бути пустим!';
        } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errorBag['email'][] = 'Введіть правильний email !';
        }

    $password = $_POST['password'];
        if(empty($password)){
            $errorBag['password'][] = 'Поле не може бути пустим!';
        } elseif(mb_strlen($password) < 6){
            $errorBag['password'][] = 'Пароль не може бути меншим шести символів!';
        }

    $password_confirm = $_POST['password_confirm'];
        if(empty($password_confirm)){
            $errorBag['password_confirm'][] = 'Поле не може бути пустим!';
        } elseif($password !== $password_confirm){
            $errorBag['password_confirm'][] = 'Паролі повинні збігатися!';
        }

        $hasErrors = false;

        foreach($errorBag as $key => $error){
            if(!empty($error)){
                $hasErrors = true;
                break;
            }
        }

        if($hasErrors) {
            $_SESSION['register_errors'] = $errorBag;

            $_SESSION['old_values'] = [
                'name' => $name,
                'login' => $login,
                'email' => $email
            ];
            
            header('Location: /to-do/auth/register.php');
            exit;
        }

$sql = 'SELECT * FROM `users` WHERE `login` = :login OR `email` = :email';

$result = $pdo->prepare($sql);
$result->execute([
                    'login' => $login,
                    'email' => $email
                ]);
$data = $result->fetch(PDO::FETCH_ASSOC);
if($data){
    if($data['login'] === $login) {
        $errorBag['login'][] = 'Такий логін вже зайнятий, спробуйте інший!';
    }
    if($data['email'] === $email) {
        $errorBag['email'][] = 'Цей email вже зареєстрований!';
    }

    $_SESSION['register_errors'] = $errorBag;
    $_SESSION['old_values'] = [
        'name' => $name,
        'login' => $login,
        'email' => $email
    ];
    header('Location: /to-do/auth/register.php');
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = 'INSERT INTO `users` (`name`, `login`, `email`, `password`) VALUES (:name, :login, :email, :password)';

$result = $pdo->prepare($sql);
$result->execute([
                    ':name' => $name,
                    ':login' => $login,
                    ':email' => $email,
                    ':password' => $hashedPassword
                ]);
unset($_SESSION['old_values']);
header('Location: /to-do/auth/login.php');
exit;