<?php
try {
    $pdo = new PDO ('mysql:host=localhost;port=3306;dbname=todo_list', 'root', 'root');
}catch(PDOException $e){
    echo 'Сталася помилка:'. $e->getMessage();
}