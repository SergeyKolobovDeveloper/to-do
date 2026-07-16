<?php

require_once '../config/db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = 'UPDATE `tasks` SET title = :title WHERE id = :id';

    $result = $pdo->prepare($sql);
    $result->bindParam(':id', $_POST['id']);
    $result->bindParam(':title', $_POST['title']);

    $result->execute();

    header('Location: ../pages/dashboard.php');
    exit;
}