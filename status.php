<?php
require_once 'db.php';

if(!empty($_GET['id'])){

    $id = (int)$_GET['id'];
    
    $sql = 'UPDATE `tasks` SET is_completed = NOT is_completed WHERE id = :id';

    $result = $pdo->prepare($sql);
    $result->bindParam(':id', $id);
    $result->execute();

    header('Location: index.php');
    exit;
}    