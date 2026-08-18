<?php
session_start();

require_once '../config/db.php';

if(!isset($_SESSION['user'])){
    header('Location:'. BASE_URL .'/auth/login.php');
    exit;
}

if(empty($_GET['id'])){
    header('Location: ../pages/dashboard.php');
    exit;
}

$id = (int)$_GET['id'];
$listId = !empty($_GET['list_id']) ? (int)$_GET['list_id'] : null;
$userId = $_SESSION['user']['id'];

$sql = 'DELETE FROM `tasks` WHERE id = :id AND user_id = :user_id';

$result = $pdo->prepare($sql);
$result->bindParam(':id', $id);
$result->bindParam(':user_id', $userId);

$result->execute();

$_SESSION['success'] = 'Задачу успішно видалено! 🗑️';

// Редірект у поточний список або на головну
header('Location: ../pages/dashboard.php' . ($listId ? '?list_id=' . $listId : ''));
exit;