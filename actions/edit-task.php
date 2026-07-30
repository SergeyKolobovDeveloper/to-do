<?php
session_start();

if(!isset($_SESSION['user'])){
    header('Location:' . BASE_URL . '/auth/login.php');
    exit;
}

require_once '../config/db.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: ../pages/dashboard.php');
    exit;
}

    $id = (int)$_POST['id'];
    $title = trim($_POST['title'] ?? '');
    $dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $userId = $_SESSION['user']['id'];

    $errorBag = [
        'title' => [],
    ];

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
        header('Location: ../pages/update.php?id=' . $id);
        exit;
    }
    $sql = 'UPDATE `tasks` SET title = :title, due_date = :due_date WHERE id = :id AND user_id = :user_id';

    $result = $pdo->prepare($sql);

    $result->execute([
        'title' => $title,
        'due_date' => $dueDate,
        'id' => $id,
        'user_id' => $userId
    ]);

    $_SESSION['success'] = 'Зміни успішно збережено! 💾';
    
    header('Location: ../pages/dashboard.php');
    exit;