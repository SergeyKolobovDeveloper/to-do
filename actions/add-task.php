<?php
session_start();

require_once '../config/db.php';
require_once '../includes/security.php';

if(!isset($_SESSION['user'])){
    header('Location:' . BASE_URL . '/auth/login.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] ==='POST'){

    $errorBag = [
        'title' => [],
        'due_date' => []
    ];

    $title = trim($_POST['title'] ?? '');
    $userId = $_SESSION['user']['id'];
    $dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

    if(empty($title)){
        $errorBag['title'][] = 'Поле не може бути пустим!';
    } elseif(mb_strlen($title) < 3){
        $errorBag['title'][] = 'Назва задачі має містити щонайменше 3 символи!';
    } elseif(mb_strlen($title) > 255){
        $errorBag['title'][] = 'Назва задачі надто довга (максимум 255 символів)!';
    }

    if(!empty($dueDate) && $dueDate < date('Y-m-d')){
        $errorBag['due_date'][] = 'Вказана дата не може бути у минулому';
    }

    if(!empty($errorBag['title']) || !empty($errorBag['due_date'])){
        $_SESSION['errors'] = $errorBag;
        $_SESSION['old'] = $_POST;
        header('Location: ../pages/create.php');
        exit;
    }

    $encryptedTitle = encryptText($title);

    $sql = 'INSERT INTO `tasks` (title, user_id, due_date) VALUES (:title, :user_id, :due_date)';

    $result = $pdo->prepare($sql);
    $result->execute([
        'title' => $encryptedTitle,
        'user_id' => $userId,
        'due_date' => $dueDate
    ]);

    $_SESSION['success'] = 'Нову задачу успішно створено! ✨';

    header('Location: ../pages/dashboard.php');
    exit;
} else {
    header('Location: ../pages/create.php');
    exit;
}