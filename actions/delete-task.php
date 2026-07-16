<?php
require_once '../config/db.php';

if(!empty($_GET['id'])){

    $id = (int)$_GET['id'];

    $sql = 'DELETE FROM `tasks` WHERE id = :id';

    $result = $pdo->prepare($sql);
    $result->bindParam(':id', $id);

    $result->execute();

    header('Location: ../pages/dashboard.php');
    exit;
}