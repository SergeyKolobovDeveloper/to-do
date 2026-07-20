<?php
session_start();

if(!isset($_SESSION['user'])){
    header('Location: /to-do/auth/login.php');
    exit;
}

require_once '../config/db.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id']) || empty($_POST['title'])) {
    header('Location: ../pages/dashboard.php');
    exit;
}

    $id = (int)$_POST['id'];
    $title = trim($_POST['title']);
    $userId = $_SESSION['user']['id'];
    $sql = 'UPDATE `tasks` SET title = :title WHERE id = :id AND user_id = :user_id';

    $result = $pdo->prepare($sql);
    $result->bindParam(':id', $id);
    $result->bindParam(':title', $title);
    $result->bindParam(':user_id', $userId);

    $result->execute();

    header('Location: ../pages/dashboard.php');
    exit;