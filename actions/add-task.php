<?php
require_once '../config/db.php';

if($_SERVER['REQUEST_METHOD'] ==='POST' && !empty($_POST['title'])){

    $title = trim($_POST['title']);
    $sql = 'INSERT INTO `tasks` (title) VALUES (:title)';

    $result = $pdo->prepare($sql);
    $result->bindParam(':title', $title);

    $result->execute();

    header('Location: ../pages/dashboard.php');
    exit;
}