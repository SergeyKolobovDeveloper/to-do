<?php
session_start();

if(!isset($_SESSION['user'])){
    header('Location:'. BASE_URL .'/auth/login.php');
    exit;
}
require_once '../config/db.php';

if(empty($_GET['id'])){

    header('Location: ../pages/dashboard.php');
    exit;
}

    $id = (int)$_GET['id'];
    $userId = $_SESSION['user']['id'];

    $sql = 'DELETE FROM `tasks` WHERE id = :id AND user_id = :user_id';

    $result = $pdo->prepare($sql);
    $result->bindParam(':id', $id);
    $result->bindParam(':user_id', $userId);

    $result->execute();

    $_SESSION['success'] = 'Задачу успішно видалено! 🗑️';

    header('Location: ../pages/dashboard.php');
    exit;