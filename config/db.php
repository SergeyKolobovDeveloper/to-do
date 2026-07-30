<?php
require_once __DIR__ . '/app.php';

$host     = 'localhost';
$dbname   = 'dbname';
$username = 'root';
$password = '';

if(file_exists(__DIR__ . '/db.local.php')){
    require_once __DIR__. '/db.local.php';
}

try {
    $pdo = new PDO ("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}catch(PDOException $e){
    echo 'Сталася помилка:'. $e->getMessage();
}