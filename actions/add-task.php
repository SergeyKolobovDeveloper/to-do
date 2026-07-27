<?php
session_start();

if(!isset($_SESSION['user'])){
    header('Location: /to-to/auth/login.php');
    exit;
}

require_once '../config/db.php';

if($_SERVER['REQUEST_METHOD'] ==='POST'){

    $errorBag = [
        'title' => []
    ];

    $title = trim($_POST['title'] ?? '');
    $userId = $_SESSION['user']['id'];

    if(empty($title)){
        $errorBag['title'][] = 'Поле не може бути пустим!';
    } elseif(mb_strlen($title) < 3){
        $errorBag['title'][] = 'Назва задачі має містити щонайменше 3 символи!';
    } elseif(mb_strlen($title) > 255){
        $errorBag['title'][] = 'Назва задачі надто довга (максимум 255 символів)!';
    }

    if(!empty($errorBag['title'])){
        $_SESSION['errors'] = $errorBag;
        $_SESSION['old'] = $_POST;
        header('Location: ../pages/create.php');
        exit;
    }

    $sql = 'INSERT INTO `tasks` (title, user_id) VALUES (:title, :user_id)';

    $result = $pdo->prepare($sql);
    $result->bindParam(':title', $title);
    $result->bindParam(':user_id', $userId);

    $result->execute();

    $_SESSION['success'] = 'Нову задачу успішно створено! ✨';

    header('Location: ../pages/dashboard.php');
    exit;
} else {
    header('Location: ../pages/create.php');
    exit;
}