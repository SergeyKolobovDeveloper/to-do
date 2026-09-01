<?php
session_start();

require_once '../config/db.php';

if(!isset($_SESSION['user'])){
    header('Location:' . BASE_URL . '/auth/login.php');
    exit;
}

if(empty($_GET['id'])){
    header('Location: ../pages/dashboard.php');
    exit;
}

$id = (int)$_GET['id'];
$userId = $_SESSION['user']['id'];
$filter = $_GET['filter'] ?? 'all';
$listId = !empty($_GET['list_id']) ? (int)$_GET['list_id'] : null;

$sql = 'UPDATE `tasks` SET is_completed = NOT is_completed WHERE id = :id AND user_id = :user_id';

$result = $pdo->prepare($sql);
$result->bindParam(':id', $id);
$result->bindParam(':user_id', $userId);
$result->execute();

// Формуємо URL для повернення із збереженням filter та list_id
$redirectUrl = '../pages/dashboard.php?filter=' . urlencode($filter);
if ($listId) {
    $redirectUrl .= '&list_id=' . $listId;
}

header('Location: ' . $redirectUrl);
exit;
   