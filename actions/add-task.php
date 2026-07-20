<?php
session_start();

if(!isset($_SESSION['user'])){
    header('Location: /to-to/auth/login.php');
    exit;
}

require_once '../config/db.php';

if($_SERVER['REQUEST_METHOD'] ==='POST' && !empty($_POST['title'])){

    $title = trim($_POST['title']);
    $userId = $_SESSION['user']['id'];

    $sql = 'INSERT INTO `tasks` (title, user_id) VALUES (:title, :user_id)';

    $result = $pdo->prepare($sql);
    $result->bindParam(':title', $title);
    $result->bindParam(':user_id', $userId);

    $result->execute();

    header('Location: ../pages/dashboard.php');
    exit;
} else {
    header('Location: ../pages/create.php');
    exit;
}